<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\DocumentType;
use App\Models\DocumentCode;
use App\Models\User;
use App\Models\DocumentAttachment;
use App\Models\DocumentHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class DocumentController extends Controller
{
    public function index(Request $request)
    {
        $pageConfigs = ['myLayout' => 'horizontal'];
         if ($request->filled('search')) {
        $query = Document::with(['documentType', 'creator', 'authorizedPerson', 'code'])
            ->latest();
         }
        $user = auth()->user();
       if (!$request->filled('search')) {
       $query = Document::with(['documentType', 'creator', 'authorizedPerson', 'code'])
        ->where(function($q) use ($user) {
            $q->where('user_id', $user->id) // Created by user
              ->orWhere('authorized_person_id', $user->id) // Authorized person
              ->orWhereHas('code', function($q) use ($user) {
                  $q->where('user_id', $user->id); // Code belongs to user
              });
        })
        ->latest();
      }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('document_number', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%")
                  ->orWhere('to_address_details', 'like', "%{$search}%")
                  ->orWhere('project_details', 'like', "%{$search}%")
                  ->orWhereHas('creator', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('authorizedPerson', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('code', function($q) use ($search) {
                      $q->where('code', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('year')) {
            $query->where('year', $request->input('year'));
        }

        if ($request->filled('type')) {
            $query->where('document_type_id', $request->input('type'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $documents = $query->paginate(20);

        $documentTypes = DocumentType::where('is_active', true)->get();
        $years = Document::select('year')->distinct()->orderBy('year', 'desc')->pluck('year');

        return view('documents.index', compact('documents', 'documentTypes', 'years'),['pageConfigs'=> $pageConfigs]);
    }

    public function create()
    {
        $pageConfigs = ['myLayout' => 'horizontal'];
        $documentTypes = DocumentType::where('is_active', true)->get();
        $codes = DocumentCode::with('user')->where('is_active', true)->get();
        $users = User::where('active', true)->get();

        return view('documents.create', compact('documentTypes', 'codes', 'users'),['pageConfigs'=> $pageConfigs]);
    }

    public function generateNumberPreview(Request $request)
    {
        $request->validate([
            'number_type' => ['required', Rule::in(['DS', 'General'])],
            'document_type_id' => 'required|exists:document_types,id',
            'code_id' => 'required|exists:document_codes,id',
        ]);

        $documentType = DocumentType::find($request->document_type_id);
        $code = DocumentCode::find($request->code_id);
        $year = date('Y');

        // Get the next sequence number
        $lastDocument = Document::where('number_type', $request->number_type)
            ->where('year', $year)
            ->orderBy('sequence_number', 'desc')
            ->first();

        $nextSequence = $lastDocument ? $lastDocument->sequence_number + 1 : 1;

        if ($request->number_type === 'DS') {
            $preview = "No.CMD/DS/{$code->code}/{$nextSequence}/{$year}";
        } else {
            $preview = "No.CMD/{$nextSequence}/{$code->code}/{$year}";
        }

        return response()->json([
            'preview' => $preview,
            'sequence_number' => $nextSequence,
            'year' => $year
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'number_type' => ['required', Rule::in(['DS', 'General'])],
            'document_type_id' => 'required|exists:document_types,id',
            'code_id' => 'required|exists:document_codes,id',
            'authorized_person_id' => 'required|exists:users,id',
            'to_address_details' => 'required|string|max:500',
            'subject' => 'required|string|max:255',
            'project_details' => 'nullable|string|max:255',
            'sequence_number' => 'required|integer|min:1',
            'year' => 'required|digits:4',
        ]);

        // Generate the document number
        $code = DocumentCode::find($validated['code_id']);

        if ($validated['number_type'] === 'DS') {
            $documentNumber = "No.CMD/DS/{$code->code}/{$validated['sequence_number']}/{$validated['year']}";
        } else {
            $documentNumber = "No.CMD/{$validated['sequence_number']}/{$code->code}/{$validated['year']}";
        }

        // Check for duplicate
        if (Document::where('document_number', $documentNumber)->exists()) {
            return back()->withInput()->withErrors(['document_number' => 'This document number already exists.']);
        }

        // Create the document
        $document = Document::create([
            'document_number' => $documentNumber,
            'number_type' => $validated['number_type'],
            'document_type_id' => $validated['document_type_id'],
            'user_id' => Auth::id(),
            'authorized_person_id' => $validated['authorized_person_id'],
            'code_id' => $validated['code_id'],
            'to_address_details' => $validated['to_address_details'],
            'subject' => $validated['subject'],
            'project_details' => $validated['project_details'],
            'sequence_number' => $validated['sequence_number'],
            'year' => $validated['year'],
            'status' => 'created',
        ]);

        // Add to history
        DocumentHistory::create([
            'document_id' => $document->id,
            'user_id' => Auth::id(),
            'action' => 'created',
            'details' => 'Document number generated'
        ]);

        return redirect()->route('documents.show', $document)->with('success', 'Document number generated successfully.');
    }

    public function show(Document $document)
    {
        $pageConfigs = ['myLayout' => 'horizontal'];
        $showFullDetails = $this->canViewFullDetails($document);
        return view('documents.show', compact('document', 'showFullDetails'),['pageConfigs'=> $pageConfigs]);
    }

    public function edit(Document $document)
{
  $pageConfigs = ['myLayout' => 'horizontal'];
    // Check if user can edit
    if (!$this->canEditDocument($document)) {
        abort(403, 'Unauthorized action.');
    }

    // Can't edit if document is confirmed
    if ($document->status !== 'created') {
        return redirect()->route('documents.show', $document)
            ->with('error', 'Cannot edit document after confirmation.');
    }

    $documentTypes = DocumentType::where('is_active', true)->get();
    $codes = DocumentCode::with('user')->where('is_active', true)->get();
    $users = User::where('active', true)->get();

    return view('documents.edit', compact('document', 'documentTypes', 'codes', 'users'),['pageConfigs'=> $pageConfigs]);
}

public function update(Request $request, Document $document)
{
    // Check if user can edit
    if (!$this->canEditDocument($document)) {
        abort(403, 'Unauthorized action.');
    }

    // Can't edit if document is confirmed
    if ($document->status !== 'created') {
        return redirect()->route('documents.show', $document)
            ->with('error', 'Cannot edit document after confirmation.');
    }

    $validated = $request->validate([
        'document_type_id' => 'required|exists:document_types,id',
        'code_id' => 'required|exists:document_codes,id',
        'authorized_person_id' => 'required|exists:users,id',
        'to_address_details' => 'required|string|max:500',
        'subject' => 'required|string|max:255',
        'project_details' => 'nullable|string|max:255',
    ]);

    $document->update($validated);

    DocumentHistory::create([
        'document_id' => $document->id,
        'user_id' => Auth::id(),
        'action' => 'updated',
        'details' => 'Document details updated'
    ]);

    return redirect()->route('documents.show', $document)
        ->with('success', 'Document updated successfully.');
}

protected function canEditDocument(Document $document)
{
    $user = auth()->user();
    return $user->id === $document->user_id || // Creator
           $user->id === $document->authorized_person_id || // Authorized person
           $user->id === $document->code->user_id; // Code owner
}

    public function uploadAttachment(Request $request, Document $document)
    {
        if ($document->status !== 'created') {
            return back()->with('error', 'Cannot upload attachment for this document in its current state.');
        }

        $request->validate([
            'attachment' => 'required|file|max:10240', // 10MB max
        ]);

        $file = $request->file('attachment');
          // $path = $file->store('public/tapal_attachments');
        $path = $file->store('public/documents');

        $attachment = DocumentAttachment::create([
            'document_id' => $document->id,
            'file_path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
        ]);

        DocumentHistory::create([
            'document_id' => $document->id,
            'user_id' => Auth::id(),
            'action' => 'attachment_uploaded',
            'details' => 'Attachment uploaded: ' . $file->getClientOriginalName()
        ]);

        return back()->with('success', 'Attachment uploaded successfully.');
    }

    public function confirmDocument(Document $document)
    {
        if ($document->status !== 'created' || $document->attachments->isEmpty()) {
            return back()->with('error', 'Document cannot be confirmed in its current state.');
        }

        $document->update(['status' => 'active']);

        DocumentHistory::create([
            'document_id' => $document->id,
            'user_id' => Auth::id(),
            'action' => 'confirmed',
            'details' => 'Document confirmed and activated'
        ]);

        return back()->with('success', 'Document confirmed and activated successfully.');
    }

    public function removeAttachment(Document $document, DocumentAttachment $attachment)
{
    $this->authorize('removeAttachment', $document);

    if ($document->status !== 'created') {
        return back()->with('error', 'Attachments can only be removed from unconfirmed documents.');
    }

    // Verify the attachment belongs to the document
    if ($attachment->document_id !== $document->id) {
        abort(404);
    }

    try {
        // Delete the file from storage
        Storage::disk('public')->delete($attachment->file_path);

        // Add to history before deleting
        DocumentHistory::create([
            'document_id' => $document->id,
            'user_id' => Auth::id(),
            'action' => 'attachment_removed',
            'details' => 'Attachment removed: ' . $attachment->original_name
        ]);

        // Delete the attachment record
        $attachment->delete();

        // return back()->with('success', 'Attachment removed successfully.');
        return redirect()->back()->with('success', 'Attachment removed successfully.');
    } catch (\Exception $e) {
        return back()->with('error', 'Failed to remove attachment: ' . $e->getMessage());
    }
}

    public function cancelDocument(Request $request, Document $document)
    {
        if (!auth()->user()->can('cancel-documents')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'cancellation_reason' => 'required|string|max:500',
        ]);

        $document->update([
            'status' => 'cancelled',
            'cancellation_reason' => $request->cancellation_reason,
            'cancelled_by' => Auth::id(),
            'cancelled_at' => now(),
        ]);

        DocumentHistory::create([
            'document_id' => $document->id,
            'user_id' => Auth::id(),
            'action' => 'cancelled',
            'details' => 'Document cancelled. Reason: ' . $request->cancellation_reason
        ]);

        return back()->with('success', 'Document cancelled successfully.');
    }

    public function downloadAttachment(DocumentAttachment $attachment)
    {
        if (!Storage::disk('public')->exists('documents/' . $attachment->file_path)) {
            abort(404);
        }

        $headers = [
            'Content-Type' => $attachment->mime_type,
            'Content-Disposition' => 'attachment; filename="' . $attachment->original_name . '"',
        ];

        return Storage::disk('public')->download($attachment->file_path, $attachment->original_name, $headers);
    }

    protected function canViewFullDetails(Document $document)
    {
        $user = auth()->user();
        return $user->id === $document->user_id || // Creator
           $user->id === $document->authorized_person_id || // Authorized person
           $user->id === $document->code->user_id; // Code owner
    }
}