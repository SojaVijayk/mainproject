@extends('layouts/layoutMaster')

@section('title', 'Leave - Request')

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


@endsection

@section('page-script')
<style>
    .timeline {
        position: relative;
        padding: 0 0 0 1rem;
        margin: 0 0 1rem 0;
    }
    .timeline::before {
        content: '';
        position: absolute;
        top: 0;
        bottom: 0;
        width: 4px;
        background: #ddd;
        left: 31px;
        margin: 0;
        border-radius: 2px;
    }
    .time-label {
        position: relative;
        margin-bottom: 1rem;
    }
    .time-label span {
        padding: 0.5rem 1rem;
        color: white;
        border-radius: 4px;
        font-weight: 600;
    }
    .timeline-item {
        position: relative;
        margin-bottom: 1rem;
    }
    .timeline-item::before {
        content: '';
        position: absolute;
        top: 0;
        left: -1.5rem;
        width: 1rem;
        height: 1rem;
        border-radius: 50%;
        background: #007bff;
        border: 3px solid white;
    }
    .timeline-header {
        margin: 0;
        padding: 0.5rem 1rem;
        background: #f4f4f4;
        border-radius: 4px;
    }
    .timeline-body {
        padding: 1rem;
        background: white;
        border: 1px solid #ddd;
        border-radius: 4px;
        margin-top: 0.5rem;
    }
    .time {
        color: #999;
        font-size: 0.8rem;
    }
</style>
{{--  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>  --}}
<script>
   <script>
    $(document).ready(function() {
        $('.select2').select2({
            placeholder: "Select users",
            allowClear: true
        });
    });
</script>
@endsection

@section('content')
@php
    $userAssignment = $tapal->movements
        ->where('to_user_id', Auth::id())
        ->where('is_assignment', true)
        ->sortByDesc('created_at')
        ->first();

     //   $lastMovement = $tapal->movements->last();
         $lastMovement = $tapal->movements->where('is_assignment',1)->last();

@endphp
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Tapal Details - {{ $tapal->tapal_number }}</h3>
        <div class="card-tools">
            @if($tapal->created_by == Auth::id()  && $lastMovement->status != 'Completed')
                <a href="{{ route('tapals.edit', $tapal->id) }}" class="btn btn-primary">
                    <i class="fas fa-edit"></i> Edit
                </a>
            @endif
            @if($tapal->current_holder_id == Auth::id() && $lastMovement->status != 'Completed')
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#forwardModal">
                    <i class="fas fa-share"></i> Forward
                </button>
                @if($tapal->movements()->where('to_user_id', Auth::id())->where('status', 'Pending')->exists())
                    <form action="{{ route('tapals.accept', $tapal->movements()->where('to_user_id', Auth::id())->latest()->first()->id) }}"
                          method="POST"
                          class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-info">
                            <i class="fas fa-check"></i> Accept
                        </button>
                    </form>
                @endif
                @if($userAssignment && $userAssignment->status !== 'Completed')
    <button type="button" class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#completeModal">
        <i class="fas fa-check-circle"></i> Take Action
    </button>

<!-- Completion Modal -->
 <div class="modal fade" id="completeModal" tabindex="-1" aria-labelledby="completeModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('tapals.complete', $userAssignment->id) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="completeModalLabel">Complete Tapal</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="completion_remarks">Completion Remarks*</label>
                            <textarea name="completion_remarks" id="completion_remarks"
                                      class="form-control" rows="5" required
                                      placeholder="Describe the actions taken to complete this tapal"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Mark as Complete</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


            @endif
            @endif
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h5>Basic Information</h5>
                <table class="table table-bordered">
                    <tr>
                        <th>Type</th>
                        <td>{{ ucfirst($tapal->type) }}</td>
                    </tr>
                    <tr>
                        <th>Inward Date</th>
                        <td>{{ $tapal->inward_date }}</td>
                    </tr>
                    <tr>
                        <th>Received Date</th>
                        <td>{{ $tapal->received_date }}</td>
                    </tr>
                    <tr>
                        <th>Mode</th>
                        <td>{{ $tapal->inward_mode }}</td>
                    </tr>
                    @if($tapal->inward_mode == 'By Mail')
                    <tr>
                        <th>Mail ID</th>
                        <td>{{ $tapal->mail_id }}</td>
                    </tr>
                    @else
                    <tr>
                        <th>From Name</th>
                        <td>{{ $tapal->from_name }}</td>
                    </tr>
                    <tr>
                        <th>Department</th>
                        <td>{{ $tapal->from_department }}</td>
                    </tr>
                    @endif
                </table>
            </div>
            <div class="col-md-6">
                <h5>Document Details</h5>
                <table class="table table-bordered">
                    <tr>
                        <th>Reference Number</th>
                        <td>{{ $tapal->ref_number ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Letter Date</th>
                        <td>{{ $tapal->letter_date ? $tapal->letter_date : 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Subject</th>
                        <td>{{ $tapal->subject }}</td>
                    </tr>
                    <tr>
                        <th>Created By</th>
                        <td>{{ $tapal->creator->name }}</td>
                    </tr>
                    <tr>
                        <th>Current Holder</th>
                        <td>{{ $tapal->currentHolder->name }}</td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>
                            {{--  @php
                                $lastMovement = $tapal->movements->last();
                            @endphp  --}}
                            @if($lastMovement)
                                <span class="badge bg-{{ $lastMovement->status == 'Completed' ? 'success' : ($lastMovement->status == 'Accepted' ? 'info' : 'warning') }}">
                                    {{ $lastMovement->status }}
                                </span>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        @if($tapal->description)
        <div class="row mt-3">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title">Description</h6>
                    </div>
                    <div class="card-body">
                        {{ $tapal->description }}
                    </div>
                </div>
            </div>
        </div>
        @endif

        @if($tapal->attachments->count() > 0)
        <div class="row mt-3">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title">Attachments</h6>
                    </div>
                    <div class="card-body">
                        <div class="list-group">
                            @foreach($tapal->attachments as $attachment)
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <i class="fas fa-file-{{ $attachment->file_type == 'application/pdf' ? 'pdf' : (str_contains($attachment->file_type, 'image') ? 'image' : 'alt') }} mr-2"></i>
                                        {{ $attachment->file_name }}
                                        <small class="text-muted ml-2">
                                            ({{ $attachment->file_type }}, {{ round($attachment->file_size / 1024) }} KB)
                                        </small>
                                    </div>
                                    <div>
                                        <a href="{{ asset('storage/' . str_replace('public/', '', $attachment->file_path)) }}"
                                           target="_blank"
                                           class="btn btn-sm btn-info mr-2">
                                           <i class="fas fa-eye"></i> View
                                        </a>
                                        {{--  @if($tapal->created_by == Auth::id())
                                        <form action="{{ route('attachments.destroy', $attachment->id) }}"
                                              method="POST"
                                              class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger"
                                                    onclick="return confirm('Delete this attachment?')">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </form>
                                        @endif  --}}
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title">Movement History</h6>
                    </div>
                    <div class="card-body">
                        <div class="timeline">
                            @foreach($tapal->movements as $movement)
                            <div class="time-label">
                                <span class="bg-{{ $movement->status == 'Completed' ? 'success' : ($movement->status == 'Accepted' ? 'info' : 'primary') }}">
                                    {{ $movement->created_at }}
                                </span>
                            </div>
                            <div>
                                <i class="fas fa-arrow-right bg-blue"></i>
                                <div class="timeline-item">
                                    <span class="time"><i class="fas fa-clock"></i> {{ $movement->created_at }}</span>
                                    <h3 class="timeline-header">
                                        {{ $movement->fromUser->name }}
                                        <i class="fas fa-arrow-right"></i>
                                        {{ $movement->toUser->name }}
                                        @if($movement->is_assignment)
                                            <span class="badge bg-success">Assignment</span>
                                        @endif
                                    </h3>

                                    <div class="timeline-body">
                                        @if($movement->status === 'Completed')
                                        <div class="alert alert-success p-2">
                                            <h5 class="m-1"><i class="fas fa-check-circle"></i> Completion Details</h5>
                                            <p class="m-1">{{ $movement->remarks }}</p>
                                            <small class="text-muted">
                                                Completed on: {{ $movement->completed_at }}
                                            </small>
                                        </div>
                                        @else
                                        <p><strong>Status:</strong>
                                            <span class="badge bg-{{ $movement->status == 'Pending' ? 'warning' : 'info' }}">
                                                {{ $movement->status }}
                                            </span>
                                        </p>
                                        @if($movement->remarks)
                                        <p><strong>Remarks:</strong> {{ $movement->remarks }}</p>
                                        @endif
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endforeach
                            <div>
                                <i class="fas fa-clock bg-gray"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Forward Modal -->
<div class="modal fade" id="forwardModal" tabindex="-1" aria-labelledby="forwardModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('tapals.forward', $tapal->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="forwardModalLabel">Forward Tapal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="assigned_user_id">Assign To*</label>
                        <select name="assigned_user_id" id="assigned_user_id" class="form-control" required>
                            <option value="">Select User</option>
                            @foreach($users as $user)
                                @if($user->id != Auth::id())
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    {{--  <div class="form-group mt-3">
                        <label for="notify_users">Notify Users (Optional)</label>
                        <select name="notify_users[]" id="notify_users" class="form-control select2" multiple>
                            @foreach($users as $user)
                                @if($user->id != Auth::id())
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>  --}}
                    <div class="form-group mt-3">
                        <label for="assignment_remarks">Remarks</label>
                        <textarea name="assignment_remarks" id="assignment_remarks" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Forward Tapal</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
