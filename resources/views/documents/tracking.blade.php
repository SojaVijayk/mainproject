@extends('layouts/layoutMaster')

@section('title', 'Document tracking')
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
<div class="container-fluid">
  <div class="row mb-4">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5>Document Management</h5>
          <div>


            <a href="{{ route('documents.export', request()->query()) }}" class="btn btn-success">
              <i class="fas fa-file-excel"></i> Export
            </a>

          </div>
        </div>
        <div class="card-body">
          <form method="GET" action="{{ route('documents.tracking') }}">
            <div class="row mb-3">
              <div class="col-md-3">
                <input type="text" name="search" class="form-control" placeholder="Search..."
                  value="{{ request('search') }}">
              </div>
              <div class="col-md-2">
                <select name="code" class="form-control">
                  <option value="">All Codes</option>
                  @foreach($codes as $code)
                  <option value="{{ $code->code }}" {{ request('code')==$code->code ? 'selected' : '' }}>
                    {{ $code->code }}
                  </option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-2">
                <select name="document_type" class="form-control">
                  <option value="">All Types</option>
                  @foreach($documentTypes as $type)
                  <option value="{{ $type->id }}" {{ request('document_type')==$type->id ? 'selected' : '' }}>
                    {{ $type->name }}
                  </option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-2">
                <select name="authorized_person" class="form-control">
                  <option value="">All Authorized</option>
                  @foreach($users as $user)
                  <option value="{{ $user->id }}" {{ request('authorized_person')==$user->id ? 'selected' : '' }}>
                    {{ $user->name }}
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
              <div class="col-md-1">
                <button type="submit" class="btn btn-primary w-100">
                  <i class="fas fa-search"></i>
                </button>
              </div>
            </div>
            <div class="row">
              <div class="col-md-3">
                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}"
                  placeholder="From Date">
              </div>
              <div class="col-md-3">
                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}"
                  placeholder="To Date">
              </div>
              <div class="col-md-3">
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" name="my_documents" id="my_documents" {{
                    request('my_documents') ? 'checked' : '' }}>
                  <label class="form-check-label" for="my_documents">
                    Show Only My Documents
                  </label>
                </div>
              </div>
              <div class="col-md-3 text-right">
                <a href="{{ route('documents.index') }}" class="btn btn-secondary">Reset Filters</a>
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
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-striped table-hover">
              <thead class="thead-dark">
                <tr>
                  <th>Document Number</th>
                  <th>Type</th>
                  <th>Subject</th>
                  <th>To Address</th>
                  <th>Code</th>
                  <th>Created By</th>
                  <th>Authorized</th>
                  <th>Status</th>
                  <th>Created At</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                @foreach($documents as $document)
                <tr>
                  <td>{{ $document->document_number }}</td>
                  <td>{{ $document->documentType->name }}</td>
                  <td>{{ Str::limit($document->subject, 30) }}</td>
                  <td>{{ Str::limit($document->to_address_details, 30) }}</td>
                  <td>
                    <span class="badge bg-info">
                      {{ $document->code->code }}
                    </span>
                  </td>
                  <td>{{ $document->creator->name }}</td>
                  <td>{{ $document->authorizedPerson->name }}</td>
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
                    <a href="{{ route('documents.tracking.show', $document) }}" class="btn btn-sm btn-info"
                      title="View">
                      <i class="fas fa-eye"></i>
                    </a>

                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
          <div class="mt-3">
            {{ $documents->appends(request()->query())->links() }}
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection