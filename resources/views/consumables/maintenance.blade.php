@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-6">
            <h1>Maintenance Report</h1>
        </div>
        <div class="col-md-6 text-right">
            <a href="{{ route('reports.maintenance') }}?export=excel" class="btn btn-success">
                <i class="fas fa-file-excel"></i> Export to Excel
            </a>
            <a href="{{ route('reports.maintenance') }}?export=pdf" class="btn btn-danger">
                <i class="fas fa-file-pdf"></i> Export to PDF
            </a>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-filter"></i> Filters
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('reports.maintenance') }}">
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
                    <a href="{{ route('reports.maintenance') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <i class="fas fa-table"></i> Maintenance Report
        </div>
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
                            <th>Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($records as $record)
                        <tr>
                            <td>{{ $record->title }}</td>
                            <td>
                                <a href="{{ route('assets.show', $record->asset) }}">
                                    {{ $record->asset->asset_tag }} - {{ $record->asset->name }}
                                </a>
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
                            <td>{{ Str::limit($record->details, 50) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="row mt-4">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h5>Summary</h5>
                        </div>
                        <div class="card-body">
                            <p><strong>Total Records:</strong> {{ $records->count() }}</p>
                            <p><strong>Total Cost:</strong> ${{ number_format($records->sum('cost'), 2) }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h5>By Status</h5>
                        </div>
                        <div class="card-body">
                            @foreach($statusSummary as $status => $count)
                                <p><strong>{{ $status }}:</strong> {{ $count }}</p>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h5>By Asset Category</h5>
                        </div>
                        <div class="card-body">
                            @foreach($categorySummary as $category => $count)
                                <p><strong>{{ $category }}:</strong> {{ $count }}</p>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection