@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-6">
            <h1>Maintenance Records</h1>
        </div>
        <div class="col-md-6 text-right">
            <a href="{{ route('maintenance.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add New Record
            </a>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-filter"></i> Filters
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('maintenance.index') }}">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="asset_id">Asset</label>
                            <select class="form-control" name="asset_id">
                                <option value="">All Assets</option>
                                @foreach($assets as $asset)
                                    <option value="{{ $asset->id }}" {{ request('asset_id') == $asset->id ? 'selected' : '' }}>
                                        {{ $asset->asset_tag }} - {{ $asset->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select class="form-control" name="status">
                                <option value="">All Statuses</option>
                                <option value="Scheduled" {{ request('status') == 'Scheduled' ? 'selected' : '' }}>Scheduled</option>
                                <option value="In Progress" {{ request('status') == 'In Progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="Completed" {{ request('status') == 'Completed' ? 'selected' : '' }}>Completed</option>
                                <option value="Cancelled" {{ request('status') == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="start_date_from">Start Date From</label>
                            <input type="date" class="form-control" name="start_date_from" value="{{ request('start_date_from') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="start_date_to">Start Date To</label>
                            <input type="date" class="form-control" name="start_date_to" value="{{ request('start_date_to') }}">
                        </div>
                    </div>
                </div>
                <div class="text-right">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                    <a href="{{ route('maintenance.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Asset</th>
                            <th>Start Date</th>
                            <th>Completion Date</th>
                            <th>Cost</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($maintenanceRecords as $record)
                        <tr>
                            <td>{{ $record->title }}</td>
                            <td>
                                <a href="{{ route('assets.show', $record->asset) }}">{{ $record->asset->asset_tag }}</a>
                            </td>
                            <td>{{ $record->start_date->format('Y-m-d') }}</td>
                            <td>
                                @if($record->completion_date)
                                    {{ $record->completion_date->format('Y-m-d') }}
                                @else
                                    N/A
                                @endif
                            </td>
                            <td>
                                @if($record->cost)
                                    ${{ number_format($record->cost, 2) }}
                                @else
                                    N/A
                                @endif
                            </td>
                            <td>
                                @if($record->status == 'Scheduled')
                                    <span class="badge badge-info">{{ $record->status }}</span>
                                @elseif($record->status == 'In Progress')
                                    <span class="badge badge-warning">{{ $record->status }}</span>
                                @elseif($record->status == 'Completed')
                                    <span class="badge badge-success">{{ $record->status }}</span>
                                @else
                                    <span class="badge badge-secondary">{{ $record->status }}</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('maintenance.show', $record) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('maintenance.edit', $record) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('maintenance.destroy', $record) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            {{ $maintenanceRecords->links() }}
        </div>
    </div>
</div>
@endsection