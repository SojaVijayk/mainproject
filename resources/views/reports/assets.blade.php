@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-6">
            <h1>Assets Report</h1>
        </div>
        <div class="col-md-6 text-right">
            <a href="{{ route('reports.assets', ['export' => true] + request()->all()) }}" class="btn btn-success">
                <i class="fas fa-file-excel"></i> Export to Excel
            </a>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-filter"></i> Filters
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('reports.assets') }}">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="search">Search</label>
                            <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Asset tag, name...">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="status_id">Status</label>
                            <select class="form-control" name="status_id">
                                <option value="">All Statuses</option>
                                @foreach($statuses as $status)
                                    <option value="{{ $status->id }}" {{ request('status_id') == $status->id ? 'selected' : '' }}>
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
                                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
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
            <i class="fas fa-table"></i> Assets Report
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Asset Tag</th>
                            <th>Name</th>
                            <th>Model</th>
                            <th>Category</th>
                            <th>Status</th>
                            <th>Assigned To</th>
                            <th>Location</th>
                            <th>Purchase Cost</th>
                            <th>Purchase Date</th>
                            <th>Warranty</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($assets as $asset)
                        <tr>
                            <td>{{ $asset->asset_tag }}</td>
                            <td>{{ $asset->name }}</td>
                            <td>{{ $asset->model->name }}</td>
                            <td>{{ $asset->model->category->name }}</td>
                            <td>{{ $asset->status->name }}</td>
                            <td>
                                @if($asset->assigned_type == 'user' && $asset->assignedUser)
                                    {{ $asset->assignedUser->name }}
                                @elseif($asset->assigned_type == 'department' && $asset->department)
                                    {{ $asset->department->name }}
                                @else
                                    Unassigned
                                @endif
                            </td>
                            <td>{{ $asset->location->name }}</td>
                            <td>${{ number_format($asset->purchase_cost, 2) }}</td>
                            <td>{{ $asset->purchase_date->format('M d, Y') }}</td>
                            <td>
                                @if($asset->warranty_expiry)
                                    {{ $asset->warranty_expiry->format('M d, Y') }}
                                    @if($asset->warranty_expiry->isFuture())
                                        ({{ $asset->warranty_expiry->diffForHumans() }})
                                    @else
                                        <span class="text-danger">(Expired)</span>
                                    @endif
                                @else
                                    N/A
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection