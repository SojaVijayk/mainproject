@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-6">
            <h1>Asset Details</h1>
        </div>
        <div class="col-md-6 text-right">
            <a href="{{ route('assets.edit', $asset) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> Edit Asset
            </a>
            @if($asset->assigned_to)
                <a href="{{ route('assets.checkin', $asset) }}" class="btn btn-warning">
                    <i class="fas fa-arrow-left"></i> Checkin
                </a>
            @else
                <a href="{{ route('assets.checkout', $asset) }}" class="btn btn-success">
                    <i class="fas fa-arrow-right"></i> Checkout
                </a>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-info-circle"></i> Basic Information
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Asset Tag:</strong> {{ $asset->asset_tag }}</p>
                            <p><strong>Name:</strong> {{ $asset->name }}</p>
                            <p><strong>Model:</strong> {{ $asset->model->name }}</p>
                            <p><strong>Category:</strong> {{ $asset->model->category->name }}</p>
                            <p><strong>Manufacturer:</strong> {{ $asset->model->manufacturer->name }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Status:</strong> 
                                <span class="badge" style="background-color: {{ $asset->status->color }};">
                                    {{ $asset->status->name }}
                                </span>
                            </p>
                            <p><strong>Assigned To:</strong> 
                                @if($asset->assigned_type == 'user' && $asset->assignedUser)
                                    {{ $asset->assignedUser->name }}
                                @elseif($asset->assigned_type == 'department' && $asset->department)
                                    {{ $asset->department->name }} (Department)
                                    @if($asset->floor)
                                        - Floor {{ $asset->floor }}
                                    @endif
                                @else
                                    Unassigned
                                @endif
                            </p>
                            <p><strong>Location:</strong> {{ $asset->location->name }}</p>
                            <p><strong>Serial Number:</strong> {{ $asset->serial_number ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-money-bill-wave"></i> Purchase Information
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Supplier:</strong> {{ $asset->supplier->name }}</p>
                            <p><strong>Purchase Cost:</strong> ${{ number_format($asset->purchase_cost, 2) }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Purchase Date:</strong> {{ $asset->purchase_date->format('M d, Y') }}</p>
                            <p><strong>Warranty Expiry:</strong> 
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
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-sticky-note"></i> Notes
                </div>
                <div class="card-body">
                    {!! $asset->notes ? nl2br(e($asset->notes)) : '<p class="text-muted">No notes available</p>' !!}
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-barcode"></i> Barcode
                </div>
                <div class="card-body text-center">
                    <img src="{{ $asset->generateBarcode() }}" alt="Barcode" class="img-fluid">
                    <p class="mt-2">{{ $asset->asset_tag }}</p>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-tools"></i> Maintenance
                    <a href="#" class="float-right" data-toggle="modal" data-target="#addMaintenanceModal">
                        <i class="fas fa-plus"></i> Add
                    </a>
                </div>
                <div class="card-body">
                    @if($asset->maintenance->count() > 0)
                        <ul class="list-group">
                            @foreach($asset->maintenance as $record)
                                <li class="list-group-item">
                                    <strong>{{ $record->title }}</strong><br>
                                    <small class="text-muted">
                                        {{ $record->start_date->format('M d, Y') }} - 
                                        @if($record->completion_date)
                                            {{ $record->completion_date->format('M d, Y') }}
                                        @else
                                            Ongoing
                                        @endif
                                    </small>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted">No maintenance records</p>
                    @endif
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-ticket-alt"></i> Recent Tickets
                    <a href="{{ route('tickets.create') }}?asset_id={{ $asset->id }}" class="float-right">
                        <i class="fas fa-plus"></i> New
                    </a>
                </div>
                <div class="card-body">
                    @if($asset->tickets->count() > 0)
                        <ul class="list-group">
                            @foreach($asset->tickets as $ticket)
                                <li class="list-group-item">
                                    <a href="{{ route('tickets.show', $ticket) }}">
                                        <strong>#{{ $ticket->ticket_number }}</strong> - {{ $ticket->title }}
                                    </a><br>
                                    <small class="text-muted">
                                        {{ $ticket->created_at->format('M d, Y') }} - 
                                        <span class="badge badge-{{ $ticket->status == 'Resolved' ? 'success' : ($ticket->status == 'Closed' ? 'secondary' : 'warning') }}">
                                            {{ $ticket->status }}
                                        </span>
                                    </small>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted">No recent tickets</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <i class="fas fa-history"></i> History
        </div>
        <div class="card-body">
            @if($asset->history->count() > 0)
                <ul class="list-group">
                    @foreach($asset->history as $history)
                        <li class="list-group-item">
                            <strong>{{ $history->action }}</strong> by {{ $history->user->name }}<br>
                            <small class="text-muted">{{ $history->created_at->format('M d, Y H:i') }}</small>
                            @if($history->details)
                                <p class="mt-1 mb-0">{{ $history->details }}</p>
                            @endif
                            @if($history->changes)
                                <ul class="mt-1">
                                    @foreach($history->changes as $field => $value)
                                        <li>{{ $field }}: {{ $value }}</li>
                                    @endforeach
                                </ul>
                            @endif
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-muted">No history available</p>
            @endif
        </div>
    </div>
</div>

<!-- Add Maintenance Modal -->
<div class="modal fade" id="addMaintenanceModal" tabindex="-1" role="dialog" aria-labelledby="addMaintenanceModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addMaintenanceModalLabel">Add Maintenance Record</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('maintenance.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="asset_id" value="{{ $asset->id }}">
                    <div class="form-group">
                        <label for="title">Title</label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>
                    <div class="form-group">
                        <label for="details">Details</label>
                        <textarea class="form-control" id="details" name="details" rows="3" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="start_date">Start Date</label>
                        <input type="date" class="form-control" id="start_date" name="start_date" required>
                    </div>
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select class="form-control" id="status" name="status" required>
                            <option value="Scheduled">Scheduled</option>
                            <option value="In Progress">In Progress</option>
                            <option value="Completed">Completed</option>
                            <option value="Cancelled">Cancelled</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection