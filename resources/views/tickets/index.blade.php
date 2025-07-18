@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-6">
            <h1>Tickets</h1>
        </div>
        <div class="col-md-6 text-right">
            <a href="{{ route('tickets.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Create New Ticket
            </a>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-filter"></i> Filters
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('tickets.index') }}">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="search">Search</label>
                            <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Ticket #, title...">
                        </div>
                    </div>
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
                </div>
                <div class="text-right">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                    <a href="{{ route('tickets.index') }}" class="btn btn-secondary">
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
                            <th>Ticket #</th>
                            <th>Title</th>
                            <th>Asset</th>
                            <th>Created By</th>
                            <th>Priority</th>
                            <th>Status</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tickets as $ticket)
                        <tr>
                            <td>{{ $ticket->ticket_number }}</td>
                            <td>{{ $ticket->title }}</td>
                            <td>
                                @if($ticket->asset)
                                    <a href="{{ route('assets.show', $ticket->asset) }}">{{ $ticket->asset->asset_tag }}</a>
                                @else
                                    N/A
                                @endif
                            </td>
                            <td>{{ $ticket->user->name }}</td>
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
                                <a href="{{ route('tickets.show', $ticket) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if(in_array($ticket->status, ['Open', 'In Progress', 'On Hold']))
                                    <a href="{{ route('tickets.edit', $ticket) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            {{ $tickets->links() }}
        </div>
    </div>
</div>
@endsection