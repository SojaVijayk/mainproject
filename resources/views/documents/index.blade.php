@extends('layouts/layoutMaster')

@section('title', 'Document Number')
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
  <div class="row mb-4">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">Letter/Document Search</div>
        <div class="card-body">
          {{-- <div class="alert alert-warning m-2" role="alert">
            🚧 This is a <strong>demo version</strong> of the system. The live platform will be launched on <strong>01
              July 2025</strong>.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
            </button>
          </div> --}}
          <form method="GET" action="{{ route('documents.index') }}">
            <div class="row">
              <div class="col-md-3">
                <input type="text" name="search" class="form-control" placeholder="Search..."
                  value="{{ request('search') }}">
              </div>
              <div class="col-md-2">
                <select name="year" class="form-control">
                  <option value="">All Years</option>
                  @foreach($years as $y)
                  <option value="{{ $y }}" {{ request('year')==$y ? 'selected' : '' }}>{{ $y }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-3">
                <select name="type" class="form-control">
                  <option value="">All Types</option>
                  @foreach($documentTypes as $type)
                  <option value="{{ $type->id }}" {{ request('type')==$type->id ? 'selected' : '' }}>{{ $type->name }}
                  </option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-2">
                <select name="status" class="form-control">
                  <option value="">All Statuses</option>
                  <option value="created" {{ request('status')=='created' ? 'selected' : '' }}>Initiated</option>
                  <option value="active" {{ request('status')=='active' ? 'selected' : '' }}>Created</option>
                  <option value="cancelled" {{ request('status')=='cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
              </div>
              <div class="col-md-2">
                <button type="submit" class="btn btn-primary">Search</button>
                <a href="{{ route('documents.index') }}" class="btn btn-secondary">Reset</a>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <span>Documents</span>
          <a href="{{ route('documents.create') }}" class="btn btn-primary">Generate New Number</a>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th>Document Number</th>
                  <th>Subject</th>
                  <th>Type</th>
                  <th>To Address</th>
                  <th>Created By</th>
                  <th>Status</th>
                  <th>Created At</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                @foreach($documents as $document)
                <tr>
                  <td>{{ $document->document_number }}</td>
                  <td>{{ Str::limit($document->subject, 30) }}</td>
                  <td>{{ $document->documentType->name }}</td>
                  <td>{{ Str::limit($document->to_address_details, 30) }}</td>
                  <td>{{ $document->creator->name }}</td>
                  <td>
                    <span class="badge
                                            @if($document->status == 'created') bg-warning
                                            @elseif($document->status == 'active') bg-success
                                            @else bg-danger @endif">
                      @if($document->status == 'created') Initiated @elseif($document->status == 'active')
                      Created @else {{ ucfirst($document->status) }} @endif
                    </span>
                  </td>
                  <td>{{ $document->created_at->format('d-m-Y') }}</td>
                  <td>
                    <a href="{{ route('documents.show', $document) }}" class="btn btn-sm btn-info">View</a>
                    @if($document->status == 'created' && (auth()->user()->id === $document->user_id ||
                    auth()->user()->id === $document->authorized_person_id ||
                    auth()->user()->id === $document->code->user_id))
                    <a href="{{ route('documents.edit', $document) }}" class="btn btn-sm btn-warning">Edit</a>
                    @endif
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
          <div class="mt-3">
            {{ $documents->links() }}
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection