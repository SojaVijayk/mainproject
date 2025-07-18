@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h2>{{ isset($ticket) ? 'Edit' : 'Create' }} Ticket</h2>
                </div>
                <div class="card-body">
                    <form action="{{ isset($ticket) ? route('tickets.update', $ticket) : route('tickets.store') }}" method="POST">
                        @csrf
                        @if(isset($ticket))
                            @method('PUT')
                        @endif
                        
                        <div class="form-group">
                            <label for="title">Title *</label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" 
                                   value="{{ old('title', $ticket->title ?? '') }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="asset_id">Related Asset</label>
                            <select class="form-control @error('asset_id') is-invalid @enderror" id="asset_id" name="asset_id">
                                <option value="">Select Asset (Optional)</option>
                                @foreach($assets as $asset)
                                    <option value="{{ $asset->id }}" 
                                        {{ old('asset_id', $ticket->asset_id ?? '') == $asset->id ? 'selected' : '' }}>
                                        {{ $asset->asset_tag }} - {{ $asset->name }} ({{ $asset->model->name }})
                                    </option>
                                @endforeach
                            </select>
                            @error('asset_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="description">Description *</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" 
                                      name="description" rows="5" required>{{ old('description', $ticket->description ?? '') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="priority">Priority *</label>
                                    <select class="form-control @error('priority') is-invalid @enderror" id="priority" name="priority" required>
                                        <option value="Low" {{ old('priority', $ticket->priority ?? '') == 'Low' ? 'selected' : '' }}>Low</option>
                                        <option value="Medium" {{ old('priority', $ticket->priority ?? '') == 'Medium' ? 'selected' : '' }}>Medium</option>
                                        <option value="High" {{ old('priority', $ticket->priority ?? '') == 'High' ? 'selected' : '' }}>High</option>
                                        <option value="Critical" {{ old('priority', $ticket->priority ?? '') == 'Critical' ? 'selected' : '' }}>Critical</option>
                                    </select>
                                    @error('priority')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="status">Status *</label>
                                    <select class="form-control @error('status') is-invalid @enderror" id="status" name="status" required>
                                        <option value="Open" {{ old('status', $ticket->status ?? '') == 'Open' ? 'selected' : '' }}>Open</option>
                                        <option value="In Progress" {{ old('status', $ticket->status ?? '') == 'In Progress' ? 'selected' : '' }}>In Progress</option>
                                        <option value="On Hold" {{ old('status', $ticket->status ?? '') == 'On Hold' ? 'selected' : '' }}>On Hold</option>
                                        @if(isset($ticket) && in_array($ticket->status, ['Resolved', 'Closed']))
                                            <option value="Resolved" {{ old('status', $ticket->status ?? '') == 'Resolved' ? 'selected' : '' }}>Resolved</option>
                                            <option value="Closed" {{ old('status', $ticket->status ?? '') == 'Closed' ? 'selected' : '' }}>Closed</option>
                                        @endif
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        @if(auth()->user()->isAdmin())
                            <div class="form-group">
                                <label for="assigned_to">Assign To</label>
                                <select class="form-control @error('assigned_to') is-invalid @enderror" id="assigned_to" name="assigned_to">
                                    <option value="">Unassigned</option>
                                    @foreach($technicians as $tech)
                                        <option value="{{ $tech->id }}" 
                                            {{ old('assigned_to', $ticket->assigned_to ?? '') == $tech->id ? 'selected' : '' }}>
                                            {{ $tech->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('assigned_to')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        @endif
                        
                        @if(isset($ticket) && in_array($ticket->status, ['Resolved', 'Closed']))
                            <div class="form-group">
                                <label for="resolution">Resolution</label>
                                <textarea class="form-control @error('resolution') is-invalid @enderror" id="resolution" 
                                          name="resolution" rows="3">{{ old('resolution', $ticket->resolution ?? '') }}</textarea>
                                @error('resolution')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        @endif
                        
                        <button type="submit" class="btn btn-primary">
                            {{ isset($ticket) ? 'Update' : 'Create' }} Ticket
                        </button>
                        <a href="{{ route('tickets.index') }}" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection