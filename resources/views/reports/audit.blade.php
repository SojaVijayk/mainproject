@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-6">
            <h1>Audit Log</h1>
        </div>
        <div class="col-md-6 text-right">
            <a href="{{ route('reports.audit') }}?export=excel" class="btn btn-success">
                <i class="fas fa-file-excel"></i> Export to Excel
            </a>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-filter"></i> Filters
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('reports.audit') }}">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="action">Action</label>
                            <select class="form-control" name="action">
                                <option value="">All Actions</option>
                                <option value="Asset Created" {{ request('action') == 'Asset Created' ? 'selected' : '' }}>Asset Created</option>
                                <option value="Asset Updated" {{ request('action') == 'Asset Updated' ? 'selected' : '' }}>Asset Updated</option>
                                <option value="Asset Checked Out" {{ request('action') == 'Asset Checked Out' ? 'selected' : '' }}>Asset Checked Out</option>
                                <option value="Asset Checked In" {{ request('action') == 'Asset Checked In' ? 'selected' : '' }}>Asset Checked In</option>
                                <option value="Maintenance Record Created" {{ request('action') == 'Maintenance Record Created' ? 'selected' : '' }}>Maintenance Record Created</option>
                                <option value="Maintenance Record Updated" {{ request('action') == 'Maintenance Record Updated' ? 'selected' : '' }}>Maintenance Record Updated</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="user_id">User</label>
                            <select class="form-control" name="user_id">
                                <option value="">All Users</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="date_from">Date From</label>
                            <input type="date" class="form-control" name="date_from" value="{{ request('date_from') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="date_to">Date To</label>
                            <input type="date" class="form-control" name="date_to" value="{{ request('date_to') }}">
                        </div>
                    </div>
                </div>
                <div class="text-right">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                    <a href="{{ route('reports.audit') }}" class="btn btn-secondary">
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
                            <th>Date/Time</th>
                            <th>User</th>
                            <th>Action</th>
                            <th>Asset</th>
                            <th>Details</th>
                            <th>Changes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($history as $record)
                        <tr>
                            <td>{{ $record->created_at->format('Y-m-d H:i') }}</td>
                            <td>{{ $record->user->name }}</td>
                            <td>{{ $record->action }}</td>
                            <td>
                                <a href="{{ route('assets.show', $record->asset) }}">
                                    {{ $record->asset->asset_tag }}
                                </a>
                            </td>
                            <td>{{ $record->details }}</td>
                            <td>
                                @if($record->changes)
                                    <ul class="list-unstyled">
                                        @foreach(json_decode($record->changes, true) as $field => $values)
                                            <li>
                                                <strong>{{ $field }}:</strong> 
                                                {{ $values['old'] ?? 'N/A' }} → {{ $values['new'] ?? 'N/A' }}
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    N/A
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            {{ $history->links() }}
        </div>
    </div>
</div>
@endsection