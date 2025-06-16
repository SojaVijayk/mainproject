<?php

namespace App\Http\Controllers;

use App\Models\Tapal;
use App\Models\TapalMovement;
use App\Models\TapalAttachment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\TapalAssigned;
use App\Mail\TapalNotification;
use Illuminate\Support\Facades\Storage;

class TapalController extends Controller
{
    public function index()

    {
       $pageConfigs = ['myLayout' => 'horizontal'];
        $tapals = Tapal::with(['creator', 'currentHolder'])
            ->where('created_by', Auth::id())
            ->orWhereHas('movements', function($query) {
                $query->where('to_user_id', Auth::id());
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return view('tapals.index', compact('tapals'),['pageConfigs'=> $pageConfigs]);
    }

    public function create()
    {
      $pageConfigs = ['myLayout' => 'horizontal'];
        $users = User::where('id', '!=', Auth::id())->get();
        return view('tapals.create', compact('users'),['pageConfigs'=> $pageConfigs]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:inward,outward',
            'inward_date' => 'required|date',
            'received_date' => 'required|date',
            'inward_mode' => 'required|in:Speed Post,Post,Courier,By Mail,By Hand',
            'mail_id' => 'nullable|required_if:inward_mode,By Mail|email',
            'from_name' => 'nullable|required_unless:inward_mode,By Mail|string|max:255',
            'from_address' => 'nullable|required_unless:inward_mode,By Mail|string',
            'from_department' => 'nullable|string|max:255',
            'from_mobile' => 'nullable|string|max:20',
            'from_person_details' => 'nullable|string',
            'ref_number' => 'nullable|string|max:100',
            'letter_date' => 'nullable|date',
            'subject' => 'required|string|max:255',
            'description' => 'nullable|string',
            'attachments.*' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
            'assigned_user_id' => 'required|exists:users,id',
            'notify_users' => 'nullable|array',
            'notify_users.*' => 'exists:users,id',
            'assignment_remarks' => 'nullable|string',
        ]);

        // Create tapal
        $tapal = Tapal::create([
            'type' => $validated['type'],
            'inward_date' => $validated['inward_date'],
            'received_date' => $validated['received_date'],
            'inward_mode' => $validated['inward_mode'],
            'mail_id' => $validated['mail_id'] ?? null,
            'from_name' => $validated['from_name'] ?? null,
            'from_address' => $validated['from_address'] ?? null,
            'from_department' => $validated['from_department'] ?? null,
            'from_mobile' => $validated['from_mobile'] ?? null,
            'from_person_details' => $validated['from_person_details'] ?? null,
            'ref_number' => $validated['ref_number'] ?? null,
            'letter_date' => $validated['letter_date'] ?? null,
            'subject' => $validated['subject'],
            'description' => $validated['description'] ?? null,
            'current_holder_id' => $validated['assigned_user_id'],
            'created_by' => Auth::id(),
        ]);

        // Handle attachments
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('public/tapal_attachments');

                TapalAttachment::create([
                    'tapal_id' => $tapal->id,
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                    'file_type' => $file->getClientMimeType(),
                    'file_size' => $file->getSize(),
                ]);
            }
        }

        // Create movement for assigned user
        $movement = TapalMovement::create([
            'tapal_id' => $tapal->id,
            'from_user_id' => Auth::id(),
            'to_user_id' => $validated['assigned_user_id'],
            'remarks' => $validated['assignment_remarks'] ?? null,
            'is_assignment' => true,
            'status' => 'Pending',
        ]);

        // Send email to assigned user
        $assignedUser = User::find($validated['assigned_user_id']);
        Mail::to($assignedUser->email)->send(new TapalAssigned($tapal, $movement, Auth::user()));

        // Handle notification emails
        foreach ($validated['notify_users'] ?? [] as $userId) {
            if ($userId != $validated['assigned_user_id']) {
                $notification = TapalMovement::create([
                    'tapal_id' => $tapal->id,
                    'from_user_id' => Auth::id(),
                    'to_user_id' => $userId,
                    'remarks' => 'For your information',
                    'is_assignment' => false,
                    'status' => 'Notified',
                ]);

                $notifyUser = User::find($userId);
                Mail::to($notifyUser->email)->send(new TapalNotification($tapal, $notification, Auth::user()));
            }
        }

        return redirect()->route('tapals.show', $tapal->id)
            ->with('success', 'Tapal created and assigned successfully!');
    }

    public function show(Tapal $tapal)
    {
        $this->authorize('view', $tapal);
$pageConfigs = ['myLayout' => 'horizontal'];
        $tapal->load(['attachments', 'movements.fromUser', 'movements.toUser', 'creator', 'currentHolder']);
        $users = User::where('id', '!=', Auth::id())->get();
        return view('tapals.show', compact('tapal', 'users'),['pageConfigs'=> $pageConfigs]);
    }

    public function edit(Tapal $tapal)
    {$pageConfigs = ['myLayout' => 'horizontal'];
        $this->authorize('update', $tapal);

        $users = User::where('id', '!=', Auth::id())->get();
        return view('tapals.edit', compact('tapal', 'users'),['pageConfigs'=> $pageConfigs]);
    }

    public function update(Request $request, Tapal $tapal)
    {
        $this->authorize('update', $tapal);

        $validated = $request->validate([
            'inward_date' => 'required|date',
            'received_date' => 'required|date',
            'inward_mode' => 'required|in:Speed Post,Post,Courier,By Mail,By Hand',
            'mail_id' => 'nullable|required_if:inward_mode,By Mail|email',
            'from_name' => 'nullable|required_unless:inward_mode,By Mail|string|max:255',
            'from_address' => 'nullable|required_unless:inward_mode,By Mail|string',
            'from_department' => 'nullable|string|max:255',
            'from_mobile' => 'nullable|string|max:20',
            'from_person_details' => 'nullable|string',
            'ref_number' => 'nullable|string|max:100',
            'letter_date' => 'nullable|date',
            'subject' => 'required|string|max:255',
            'description' => 'nullable|string',
            'attachments.*' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
        ]);

        $tapal->update($validated);

        // Handle new attachments
    if ($request->hasFile('new_attachments')) {
        foreach ($request->file('new_attachments') as $file) {
            $path = $file->store('public/tapal_attachments');

            TapalAttachment::create([
                'tapal_id' => $tapal->id,
                'file_path' => $path,
                'file_name' => $file->getClientOriginalName(),
                'file_type' => $file->getClientMimeType(),
                'file_size' => $file->getSize(),
            ]);
        }
    }

        return redirect()->route('tapals.show', $tapal->id)
            ->with('success', 'Tapal updated successfully!');
    }

    public function destroy(Tapal $tapal)
    {
        $this->authorize('delete', $tapal);

        $tapal->delete();
        return redirect()->route('tapals.index')
            ->with('success', 'Tapal deleted successfully!');
    }

    public function forward(Request $request, Tapal $tapal)
    {
        $this->authorize('forward', $tapal);

        $validated = $request->validate([
            'assigned_user_id' => 'required|exists:users,id',
            'notify_users' => 'nullable|array',
            'notify_users.*' => 'exists:users,id',
            'assignment_remarks' => 'nullable|string',
        ]);

        // Update current holder
        $tapal->update(['current_holder_id' => $validated['assigned_user_id']]);

        // Create movement for assigned user
        $movement = TapalMovement::create([
            'tapal_id' => $tapal->id,
            'from_user_id' => Auth::id(),
            'to_user_id' => $validated['assigned_user_id'],
            'remarks' => $validated['assignment_remarks'] ?? null,
            'is_assignment' => true,
            'status' => 'Pending',
        ]);

        // Send email to assigned user
        $assignedUser = User::find($validated['assigned_user_id']);
        Mail::to($assignedUser->email)->send(new TapalAssigned($tapal, $movement, Auth::user()));

        // Handle notification emails
        foreach ($validated['notify_users'] ?? [] as $userId) {
            if ($userId != $validated['assigned_user_id']) {
                $notification = TapalMovement::create([
                    'tapal_id' => $tapal->id,
                    'from_user_id' => Auth::id(),
                    'to_user_id' => $userId,
                    'remarks' => 'For your information',
                    'is_assignment' => false,
                    'status' => 'Notified',
                ]);

                $notifyUser = User::find($userId);
                Mail::to($notifyUser->email)->send(new TapalNotification($tapal, $notification, Auth::user()));
            }
        }

        return redirect()->route('tapals.show', $tapal->id)
            ->with('success', 'Tapal forwarded successfully!');
    }

    public function accept(TapalMovement $movement)
    {
        $this->authorize('accept', $movement);

        $movement->update([
            'status' => 'Accepted',
            'accepted_at' => now(),
        ]);

        return redirect()->route('tapals.show', $movement->tapal_id)
            ->with('success', 'Tapal accepted successfully!');
    }

    public function complete(Request $request, TapalMovement $movement)
    {
         $this->authorize('complete', $movement);
         // Double check the movement belongs to current user
    if ($movement->to_user_id !== Auth::id()) {
        abort(403, 'Unauthorized action');
    }

    // Check if already completed
    if ($movement->status === 'Completed') {
        return redirect()->back()
            ->with('error', 'This tapal is already completed');
    }

    $validated = $request->validate([
        'completion_remarks' => 'required|string|max:500'
    ]);

    $movement->update([
        'status' => 'Completed',
        'completed_at' => now(),
        'remarks' => $validated['completion_remarks'] // Store completion remarks
    ]);

    return redirect()->route('tapals.show', $movement->tapal_id)
        ->with('success', 'Tapal marked as completed!');
    }

    public function tracing(Request $request)
    {
      $pageConfigs = ['myLayout' => 'horizontal'];
        $query = Tapal::query()->with(['creator', 'currentHolder', 'movements.fromUser', 'movements.toUser']);

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('tapal_number', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%")
                  ->orWhere('ref_number', 'like', "%{$search}%")
                  ->orWhere('from_name', 'like', "%{$search}%")
                  ->orWhere('from_address', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->has('from_date') && $request->input('from_date') != '') {
            $query->whereDate('inward_date', '>=', $request->input('from_date'));
        }

        if ($request->has('to_date') && $request->input('to_date') != '') {
            $query->whereDate('inward_date', '<=', $request->input('to_date'));
        }

        $tapals = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('tapals.tracing', compact('tapals'),['pageConfigs'=> $pageConfigs]);
    }

    public function tracingShow(Tapal $tapal)
    {
       $pageConfigs = ['myLayout' => 'horizontal'];
        $tapal->load(['creator', 'currentHolder', 'movements.fromUser', 'movements.toUser', 'attachments']);
        return view('tapals.tracing_show', compact('tapal'),['pageConfigs'=> $pageConfigs]);
    }
}
