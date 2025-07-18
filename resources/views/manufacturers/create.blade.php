@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h2>{{ isset($manufacturer) ? 'Edit' : 'Create' }} Manufacturer</h2>
                </div>
                <div class="card-body">
                    <form action="{{ isset($manufacturer) ? route('manufacturers.update', $manufacturer) : route('manufacturers.store') }}" method="POST">
                        @csrf
                        @if(isset($manufacturer))
                            @method('PUT')
                        @endif
                        
                        <div class="form-group">
                            <label for="name">Name *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" 
                                   value="{{ old('name', $manufacturer->name ?? '') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="support_email">Support Email</label>
                                    <input type="email" class="form-control @error('support_email') is-invalid @enderror" id="support_email" 
                                           name="support_email" value="{{ old('support_email', $manufacturer->support_email ?? '') }}">
                                    @error('support_email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="support_phone">Support Phone</label>
                                    <input type="text" class="form-control @error('support_phone') is-invalid @enderror" id="support_phone" 
                                           name="support_phone" value="{{ old('support_phone', $manufacturer->support_phone ?? '') }}">
                                    @error('support_phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="support_url">Support URL</label>
                            <input type="url" class="form-control @error('support_url') is-invalid @enderror" id="support_url" 
                                   name="support_url" value="{{ old('support_url', $manufacturer->support_url ?? '') }}">
                            @error('support_url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <button type="submit" class="btn btn-primary">
                            {{ isset($manufacturer) ? 'Update' : 'Create' }} Manufacturer
                        </button>
                        <a href="{{ route('manufacturers.index') }}" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection