@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h2>{{ isset($record) ? 'Edit' : 'Create' }} Maintenance Record</h2>
                </div>
                <div class="card-body">
                    <form action="{{ isset($record) ? route('maintenance.update', $record) : route('maintenance.store') }}" method="POST">
                        @csrf
                        @if(isset($record))
                            @method('PUT')
                        @endif
                        
                        <div class="form-group">
                            <label for="asset_id">Asset *</label>
                            <select class="form-control @error('asset_id') is-invalid @enderror" id="asset_id" name="asset_id" required>
                                <option value="">Select Asset</option>
                                @foreach($assets as $asset)
                                    <option value="{{ $asset->id }}" 
                                        {{ old('asset_id', $record->asset_id ?? '') == $asset->id ? 'selected' : '' }}>
                                        {{ $asset->asset_tag }} - {{ $asset->name }} ({{ $asset->model->name }})
                                    </option>
                                @endforeach
                            </select>
                            @error('asset_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="title">Title *</label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" 
                                   value="{{ old('title', $record->title ?? '') }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="details">Details</label>
                            <textarea class="form-control @error('details') is-invalid @enderror" id="details" 
                                      name="details" rows="3">{{ old('details', $record->details ?? '') }}</textarea>
                            @error('details')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="start_date">Start Date *</label>
                                    <input type="date" class="form-control @error('start_date') is-invalid @enderror" id="start_date" 
                                           name="start_date" value="{{ old('start_date', isset($record) ? $record->start_date->format('Y-m-d') : '') }}" required>
                                    @error('start_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="completion_date">Completion Date</label>
                                    <input type="date" class="form-control @error('completion_date') is-invalid @enderror" id="completion_date" 
                                           name="completion_date" value="{{ old('completion_date', isset($record) && $record->completion_date ? $record->completion_date->format('Y-m-d') : '') }}">
                                    @error('completion_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="cost">Cost</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">$</span>
                                        </div>
                                        <input type="number" step="0.01" class="form-control @error('cost') is-invalid @enderror" id="cost" 
                                               name="cost" value="{{ old('cost', $record->cost ?? '') }}">
                                        @error('cost')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="status">Status *</label>
                                    <select class="form-control @error('status') is-invalid @enderror" id="status" name="status" required>
                                        <option value="Scheduled" {{ old('status', $record->status ?? '') == 'Scheduled' ? 'selected' : '' }}>Scheduled</option>
                                        <option value="In Progress" {{ old('status', $record->status ?? '') == 'In Progress' ? 'selected' : '' }}>In Progress</option>
                                        <option value="Completed" {{ old('status', $record->status ?? '') == 'Completed' ? 'selected' : '' }}>Completed</option>
                                        <option value="Cancelled" {{ old('status', $record->status ?? '') == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">
                            {{ isset($record) ? 'Update' : 'Create' }} Record
                        </button>
                        <a href="{{ route('maintenance.index') }}" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection