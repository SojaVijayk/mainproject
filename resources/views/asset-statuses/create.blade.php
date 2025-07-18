@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h2>{{ isset($status) ? 'Edit' : 'Create' }} Asset Status</h2>
                </div>
                <div class="card-body">
                    <form action="{{ isset($status) ? route('asset-statuses.update', $status) : route('asset-statuses.store') }}" method="POST">
                        @csrf
                        @if(isset($status))
                            @method('PUT')
                        @endif
                        
                        <div class="form-group">
                            <label for="name">Name *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" 
                                   value="{{ old('name', $status->name ?? '') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="color">Color *</label>
                            <div class="input-group colorpicker">
                                <input type="text" class="form-control @error('color') is-invalid @enderror" id="color" 
                                       name="color" value="{{ old('color', $status->color ?? '#cccccc') }}" required>
                                <div class="input-group-append">
                                    <span class="input-group-text"><i></i></span>
                                </div>
                                @error('color')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="notes">Notes</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" 
                                      name="notes" rows="3">{{ old('notes', $status->notes ?? '') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        @if(!isset($status) || !$status->is_default)
                            <div class="form-group">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_default" name="is_default" 
                                           {{ old('is_default', $status->is_default ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_default">
                                        Set as default status for new assets
                                    </label>
                                </div>
                            </div>
                        @endif
                        
                        <button type="submit" class="btn btn-primary">
                            {{ isset($status) ? 'Update' : 'Create' }} Status
                        </button>
                        <a href="{{ route('asset-statuses.index') }}" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('.colorpicker').colorpicker();
    });
</script>
@endpush