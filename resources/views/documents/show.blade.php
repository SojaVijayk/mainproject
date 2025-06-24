@extends('layouts/layoutMaster')

@section('title', 'Document Show')
@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/formvalidation/dist/css/formValidation.min.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/animate-css/animate.css')}}" />

<link rel="stylesheet" href="{{asset('assets/vendor/libs/sweetalert2/sweetalert2.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/spinkit/spinkit.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}" />

<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/spinkit/spinkit.css')}}" />



@endsection

@section('page-style')
@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js')}}"></script>

<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/FormValidation.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/sweetalert2/sweetalert2.js')}}"></script>
<script src="{{asset('assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.js')}}"></script>
<script src="{{asset('assets/vendor/libs/block-ui/block-ui.js')}}"></script>
<script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>

<script src='https://cdn.jsdelivr.net/npm/fullcalendar-scheduler@6.1.15/index.global.min.js'></script>

@endsection

@section('page-script')
@endsection

@section('content')
<div class="container">
  <div class="row justify-content-center">
    <div class="col-md-8">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <span>Document Details</span>
          <span class="badge
                        @if($document->status == 'created') bg-warning
                        @elseif($document->status == 'active') bg-success
                        @else bg-danger @endif">
            {{ ucfirst($document->status) }}
          </span>

          @if($document->status == 'created' && (auth()->user()->id === $document->user_id ||
          auth()->user()->id === $document->authorized_person_id ||
          auth()->user()->id === $document->code->user_id))
          <a href="{{ route('documents.edit', $document) }}" class="btn btn-sm btn-warning">Edit</a>
          @endif

        </div>
        <div class="card-body">
          <div class="row mb-3">
            <label class="col-md-4 col-form-label text-md-end">Document Number</label>
            <div class="col-md-6">
              <p class="form-control-plaintext font-weight-bold">{{ $document->document_number }}</p>
            </div>
          </div>

          <div class="row mb-3">
            <label class="col-md-4 col-form-label text-md-end">Document Type</label>
            <div class="col-md-6">
              <p class="form-control-plaintext">{{ $document->documentType->name }}</p>
            </div>
          </div>

          @if($showFullDetails)
          {{-- @if($document->status == 'created' && (auth()->user()->id === $document->user_id ||
          auth()->user()->id === $document->authorized_person_id ||
          auth()->user()->id === $document->code->user_id)) --}}
          <div class="row mb-3">
            <label class="col-md-4 col-form-label text-md-end">To Address Details</label>
            <div class="col-md-6">
              <p class="form-control-plaintext">{{ $document->to_address_details }}</p>
            </div>
          </div>

          <div class="row mb-3">
            <label class="col-md-4 col-form-label text-md-end">Subject</label>
            <div class="col-md-6">
              <p class="form-control-plaintext">{{ $document->subject }}</p>
            </div>
          </div>

          <div class="row mb-3">
            <label class="col-md-4 col-form-label text-md-end">Project Details</label>
            <div class="col-md-6">
              <p class="form-control-plaintext">{{ $document->project_details ?? 'N/A' }}</p>
            </div>
          </div>

          <div class="row mb-3">
            <label class="col-md-4 col-form-label text-md-end">Code</label>
            <div class="col-md-6">
              <p class="form-control-plaintext">{{ $document->code->code }} ({{ $document->code->user->name }})</p>
            </div>
          </div>

          <div class="row mb-3">
            <label class="col-md-4 col-form-label text-md-end">Authorized Person</label>
            <div class="col-md-6">
              <p class="form-control-plaintext">{{ $document->authorizedPerson->name }}</p>
            </div>
          </div>

          <div class="row mb-3">
            <label class="col-md-4 col-form-label text-md-end">Created By</label>
            <div class="col-md-6">
              <p class="form-control-plaintext">{{ $document->creator->name }}</p>
            </div>
          </div>

          <div class="row mb-3">
            <label class="col-md-4 col-form-label text-md-end">Created At</label>
            <div class="col-md-6">
              <p class="form-control-plaintext">{{ $document->created_at->format('d-m-Y H:i') }}</p>
            </div>
          </div>

          @if($document->status == 'cancelled')
          <div class="row mb-3">
            <label class="col-md-4 col-form-label text-md-end">Cancellation Reason</label>
            <div class="col-md-6">
              <p class="form-control-plaintext">{{ $document->cancellation_reason }}</p>
            </div>
          </div>

          <div class="row mb-3">
            <label class="col-md-4 col-form-label text-md-end">Cancelled By</label>
            <div class="col-md-6">
              <p class="form-control-plaintext">{{ $document->cancelledBy->name }}</p>
            </div>
          </div>

          <div class="row mb-3">
            <label class="col-md-4 col-form-label text-md-end">Cancelled At</label>
            <div class="col-md-6">
              <p class="form-control-plaintext">{{ $document->cancelled_at->format('d-m-Y H:i') }}</p>
            </div>
          </div>
          @endif

          <hr>

          <h5>Attachments</h5>
          @if($document->attachments->count() > 0)
          <ul class="list-group mb-3">
            @foreach($document->attachments as $attachment)
            <li class="list-group-item d-flex justify-content-between align-items-center">
              <div>
                {{ $attachment->original_name }}
                <br>
                <small class="text-muted">
                  Uploaded: {{ $attachment->created_at->format('d-m-Y H:i') }}
                </small>
              </div>
              <div>
                {{-- <a href="{{ route('documents.attachment.download', $attachment) }}" class="btn btn-sm btn-primary">
                  Download
                </a> --}}
                <a href="{{ asset('storage/' . str_replace('public/', '', $attachment->file_path)) }}" target="_blank"
                  class="btn btn-sm btn-primary mr-2">
                  <i class="fas fa-eye"></i> Download
                </a>
                @if($document->status == 'created')
                <form
                  action="{{ route('documents.attachment.remove', ['document' => $document, 'attachment' => $attachment]) }}"
                  method="POST" style="display: inline-block;"
                  onsubmit="return confirm('Are you sure you want to remove this attachment?')">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-danger">
                    Remove
                  </button>
                </form>
                @endif
              </div>
            </li>
            @endforeach
          </ul>
          @else
          <p>No attachments found for this document.</p>
          @endif



          @if($document->status == 'created')
          <hr>
          <h5>Upload Attachment</h5>
          <form method="POST" action="{{ route('documents.upload', $document) }}" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
              <input type="file" class="form-control" name="attachment" required>
            </div>
            <button type="submit" class="btn btn-primary">Upload</button>
          </form>

          @if($document->attachments->count() > 0)
          <hr>
          <form method="POST" action="{{ route('documents.confirm', $document) }}">
            @csrf
            <button type="submit" class="btn btn-success">Confirm Document</button>
          </form>
          @endif
          @endif

          @if($document->status != 'cancelled')
          <hr>
          <h5>Cancel Document</h5>
          <form method="POST" action="{{ route('documents.cancel', $document) }}">
            @csrf
            <div class="mb-3">
              <textarea class="form-control" name="cancellation_reason" placeholder="Cancellation reason"
                required></textarea>
            </div>
            <button type="submit" class="btn btn-danger">Cancel Document</button>
          </form>
          @endif




          @else
          <div class="alert alert-primary">
            You don't have permission to view all details of this document.
          </div>
          @endif



          <hr>
          <h5>History</h5>
          <ul class="list-group">
            @foreach($document->histories as $history)
            <li class="list-group-item">
              <strong>{{ $history->user->name }}</strong> -
              {{ ucfirst($history->action) }} -
              {{ $history->created_at->format('d-m-Y H:i') }}
              @if($history->details)
              <br><small>{{ $history->details }}</small>
              @endif
            </li>
            @endforeach
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection