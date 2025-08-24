@extends('layouts/layoutMaster')

@section('title', 'Requirements')

@section('vendor-style')
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}">
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}">
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css') }}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/formvalidation/dist/css/formValidation.min.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/animate-css/animate.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/sweetalert2/sweetalert2.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/spinkit/spinkit.css')}}" />

@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js')}}"></script>
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/FormValidation.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/sweetalert2/sweetalert2.js')}}"></script>
<script src="{{asset('assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.js')}}"></script>
<script src="{{asset('assets/vendor/libs/block-ui/block-ui.js')}}"></script>

@endsection

@section('page-script')
<script>
  $(document).ready(function() {
    $('.table').DataTable({
        responsive: true,
        pageLength: 10,
        lengthMenu: [10, 25, 50, 100],
        language: {
            paginate: {
                previous: 'Prev',
                next: 'Next'
            }
        }
    });
});
</script>
@endsection
{{-- @section('actions')
<a href="{{ route('pms.requirements.create') }}" class="btn btn-sm btn-primary">
  <i class="fas fa-plus"></i> Add New
</a>
@endsection --}}

@section('content')
<div class="card">
  <div class="card-body">
    <a href="{{ route('pms.requirements.create') }}" class="btn btn-sm btn-primary">
      <i class="fas fa-plus"></i> Add New
    </a>

    <a href="{{ route('pms.proposals.index') }}" class="btn btn-sm btn-primary">Proposals List</a>
    <div class="card-datatable table-responsive">
      <table class="table table-hover border-top">
        <thead>
          <tr>
            {{-- <th>Temp No</th> --}}
            <th>Type</th>
            <th>Client</th>
            <th>Category</th>
            <th>Project</th>
            <th>Status</th>
            <th>Proposal Status</th>
            <th>Created At</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($requirements as $requirement)
          <tr>
            {{-- <td>{{ $requirement->temp_no }}</td> --}}
            <td>{{ $requirement->type_name }}</td>
            <td>{{ $requirement->client->client_name }}</td>
            <td>{{ $requirement->category->name }}</td>
            <td>{{ $requirement->project_title }}</td>
            <td>
              <span class="badge bg-{{ $requirement->status_badge_color }}">
                {{ $requirement->status_name }}
              </span>
            </td>
            <td> {{ $requirement->proposal_status == 1 ? 'Created' : 'Pending'}}</td>
            <td>{{ $requirement->created_at != Null ? $requirement->created_at->format('d M Y') : "" }}</td>
            <td>
              <a href="{{ route('pms.requirements.show', $requirement->id) }}" class="btn btn-sm btn-info">
                <i class="fas fa-eye"></i>
              </a>
              @if($requirement->status == \App\Models\PMS\Requirement::STATUS_INITIATED && auth()->user()->id ==
              $requirement->created_by)
              <a href="{{ route('pms.requirements.edit', $requirement->id) }}" class="btn btn-sm btn-primary">
                <i class="fas fa-edit"></i>
              </a>
              @endif
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="7" class="text-center">No requirements found</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    {{-- {{ $requirements->links() }} --}}
  </div>
</div>
@endsection