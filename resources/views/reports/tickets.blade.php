@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-6">
            <h1>Tickets Report</h1>
        </div>
        <div class="col-md-6 text-right">
            <a href="{{ route('reports.tickets') }}?export=excel" class="btn btn-success">
                <i class="fas fa-file-excel"></i> Export to Excel
            </a>
            <a href="{{ route('reports.tickets') }}?export=pdf" class="btn btn-danger">
                <i class="fas fa-file-pdf"></i> Export to PDF
            </a>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-filter"></i> Filters
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('reports.tickets') }}">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select class="form-control" name="status">
                                <option value="">All Statuses</option>
                                <option value="Open" {{ request('status') == 'Open' ? 'selected' : '' }}>Open</option>
                                <option value="In Progress" {{ request('status') == 'In Progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="On Hold" {{ request('status') == 'On Hold' ? 'selected' : '' }}>On Hold</option>
                                <option value="Resolved" {{ request('status') == 'Resolved' ? 'selected' : '' }}>Resolved</option>
                                <option value="Closed" {{ request('status') == 'Closed' ? 'selected' : '' }}>Closed</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="priority">Priority</label>
                            <select class="form-control" name="priority">
                                <option value="">All Priorities</option>
                                <option value="Low" {{ request('priority') == 'Low' ? 'selected' : '' }}>Low</option>
                                <option value="Medium" {{ request('priority') == 'Medium' ? 'selected' : '' }}>Medium</option>
                                <option value="High" {{ request('priority') == 'High' ? 'selected' : '' }}>High</option>
                                <option value="Critical" {{ request('priority') == 'Critical' ? 'selected' : '' }}>Critical</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="created_from">Created From</label>
                            <input type="date" class="form-control" name="created_from" value="{{ request('created_from') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="created_to">Created To</label>
                            <input type="date" class="form-control" name="created_to" value="{{ request('created_to') }}">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="user_id">Created By</label>
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
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="assigned_to">Assigned To</label>
                            <select class="form-control" name="assigned_to">
                                <option value="">All Technicians</option>
                                @foreach($technicians as $tech)
                                    <option value="{{ $tech->id }}" {{ request('assigned_to') == $tech->id ? 'selected' : '' }}>
                                        {{ $tech->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="text-right">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                    <a href="{{ route('reports.tickets') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <i class="fas fa-table"></i> Tickets Report
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Ticket #</th>
                            <th>Title</th>
                            <th>Created By</th>
                            <th>Assigned To</th>
                            <th>Priority</th>
                            <th>Status</th>
                            <th>Created At</th>
                            <th>Resolved At</th>
                            <th>Asset</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tickets as $ticket)
                        <tr>
                            <td>{{ $ticket->ticket_number }}</td>
                            <td>{{ $ticket->title }}</td>
                            <td>{{ $ticket->user->name }}</td>
                            <td>
                                @if($ticket->assignedTo)
                                    {{ $ticket->assignedTo->name }}
                                @else
                                    Unassigned
                                @endif
                            </td>
                            <td>
                                @if($ticket->priority == 'Low')
                                    <span class="badge badge-info">{{ $ticket->priority }}</span>
                                @elseif($ticket->priority == 'Medium')
                                    <span class="badge badge-primary">{{ $ticket->priority }}</span>
                                @elseif($ticket->priority == 'High')
                                    <span class="badge badge-warning">{{ $ticket->priority }}</span>
                                @else
                                    <span class="badge badge-danger">{{ $ticket->priority }}</span>
                                @endif
                            </td>
                            <td>
                                @if($ticket->status == 'Open')
                                    <span class="badge badge-primary">{{ $ticket->status }}</span>
                                @elseif($ticket->status == 'In Progress')
                                    <span class="badge badge-warning">{{ $ticket->status }}</span>
                                @elseif($ticket->status == 'On Hold')
                                    <span class="badge badge-secondary">{{ $ticket->status }}</span>
                                @elseif($ticket->status == 'Resolved')
                                    <span class="badge badge-success">{{ $ticket->status }}</span>
                                @else
                                    <span class="badge badge-dark">{{ $ticket->status }}</span>
                                @endif
                            </td>
                            <td>{{ $ticket->created_at->format('Y-m-d H:i') }}</td>
                            <td>
                                @if($ticket->resolved_at)
                                    {{ $ticket->resolved_at->format('Y-m-d H:i') }}
                                @else
                                    N/A
                                @endif
                            </td>
                            <td>
                                @if($ticket->asset)
                                    <a href="{{ route('assets.show', $ticket->asset) }}">
                                        {{ $ticket->asset->asset_tag }}
                                    </a>
                                @else
                                    N/A
                                @endif
                            </td>
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
                            <p><strong>Total Tickets:</strong> {{ $tickets->count() }}</p>
                            <p><strong>Open Tickets:</strong> {{ $tickets->whereIn('status', ['Open', 'In Progress', 'On Hold'])->count() }}</p>
                            <p><strong>Closed Tickets:</strong> {{ $tickets->whereIn('status', ['Resolved', 'Closed'])->count() }}</p>
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
                            <h5>By Priority</h5>
                        </div>
                        <div class="card-body">
                            @foreach($prioritySummary as $priority => $count)
                                <p><strong>{{ $priority }}:</strong> {{ $count }}</p>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card mt-4">
                <div class="card-header">
                    <h5>Resolution Time</h5>
                </div>
                <div class="card-body">
                    @if($resolutionTimeSummary)
                        <p><strong>Average Resolution Time:</strong> {{ $resolutionTimeSummary['average'] }} days</p>
                        <p><strong>Fastest Resolution:</strong> {{ $resolutionTimeSummary['fastest'] }} days</p>
                        <p><strong>Slowest Resolution:</strong> {{ $resolutionTimeSummary['slowest'] }} days</p>
                    @else
                        <p>No resolution time data available.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection