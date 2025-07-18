@extends('layouts/layoutMaster')

@section('title', 'Assets')
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
    <div class="col-md-6">
      <h1>Asset Management</h1>
    </div>
    <div class="col-md-6 text-right">
      <a href="{{ route('asset.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Add New Asset
      </a>
    </div>
  </div>

  <div class="card mb-4">
    <div class="card-header">
      <i class="fas fa-filter"></i> Filters
    </div>
    <div class="card-body">
      <form method="GET" action="{{ route('asset.index') }}">
        <div class="row">
          <div class="col-md-3">
            <div class="form-group">
              <label for="search">Search</label>
              <input type="text" class="form-control" name="search" value="{{ request('search') }}"
                placeholder="Asset tag, name...">
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              <label for="status_id">Status</label>
              <select class="form-control" name="status_id">
                <option value="">All Statuses</option>
                @foreach($statuses as $status)
                <option value="{{ $status->id }}" {{ request('status_id')==$status->id ? 'selected' : '' }}>
                  {{ $status->name }}
                </option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              <label for="category_id">Category</label>
              <select class="form-control" name="category_id">
                <option value="">All Categories</option>
                @foreach($categories as $category)
                <option value="{{ $category->id }}" {{ request('category_id')==$category->id ? 'selected' : '' }}>
                  {{ $category->name }}
                </option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              <label>&nbsp;</label>
              <button type="submit" class="btn btn-primary btn-block">
                <i class="fas fa-filter"></i> Filter
              </button>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>

  <div class="card">
    <div class="card-header">
      <i class="fas fa-table"></i> Assets
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered table-hover">
          <thead>
            <tr>
              <th>Asset Tag</th>
              <th>Name</th>
              <th>Model</th>
              <th>Status</th>
              <th>Assigned To</th>
              <th>Location</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            @foreach($assets as $asset)
            <tr>
              <td>{{ $asset->asset_tag }}</td>
              <td>{{ $asset->name }}</td>
              <td>{{ $asset->model->name }}</td>
              <td>
                <span class="badge" style="background-color: {{ $asset->status->color }};">
                  {{ $asset->status->name }}
                </span>
              </td>
              <td>
                @if($asset->assigned_type == 'user' && $asset->assignedUser)
                {{ $asset->assignedUser->name }}
                @elseif($asset->assigned_type == 'department' && $asset->department)
                {{ $asset->department->name }} (Department)
                @else
                Unassigned
                @endif
              </td>
              <td>{{ $asset->location->name }}</td>
              <td>
                <a href="{{ route('asset.show', $asset) }}" class="btn btn-sm btn-info" title="View">
                  <i class="fas fa-eye"></i>
                </a>
                <a href="{{ route('asset.edit', $asset) }}" class="btn btn-sm btn-primary" title="Edit">
                  <i class="fas fa-edit"></i>
                </a>
                @if($asset->assigned_to)
                <a href="{{ route('assets.checkin', $asset) }}" class="btn btn-sm btn-warning" title="Checkin">
                  <i class="fas fa-arrow-left"></i>
                </a>
                @else
                <a href="{{ route('assets.checkout', $asset) }}" class="btn btn-sm btn-success" title="Checkout">
                  <i class="fas fa-arrow-right"></i>
                </a>
                @endif
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      {{ $assets->links() }}
    </div>
  </div>
</div>
@endsection