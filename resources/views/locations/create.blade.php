@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h2>{{ isset($location) ? 'Edit' : 'Create' }} Location</h2>
                </div>
                <div class="card-body">
                    <form action="{{ isset($location) ? route('locations.update', $location) : route('locations.store') }}" method="POST">
                        @csrf
                        @if(isset($location))
                            @method('PUT')
                        @endif
                        
                        <div class="form-group">
                            <label for="name">Name *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" 
                                   value="{{ old('name', $location->name ?? '') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="address">Address</label>
                            <textarea class="form-control @error('address') is-invalid @enderror" id="address" 
                                      name="address" rows="2">{{ old('address', $location->address ?? '') }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="city">City</label>
                                    <input type="text" class="form-control @error('city') is-invalid @enderror" id="city" 
                                           name="city" value="{{ old('city', $location->city ?? '') }}">
                                    @error('city')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="state">State/Province</label>
                                    <input type="text" class="form-control @error('state') is-invalid @enderror" id="state" 
                                           name="state" value="{{ old('state', $location->state ?? '') }}">
                                    @error('state')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="zip_code">Zip/Postal Code</label>
                                    <input type="text" class="form-control @error('zip_code') is-invalid @enderror" id="zip_code" 
                                           name="zip_code" value="{{ old('zip_code', $location->zip_code ?? '') }}">
                                    @error('zip_code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="country">Country</label>
                            <input type="text" class="form-control @error('country') is-invalid @enderror" id="country" 
                                   name="country" value="{{ old('country', $location->country ?? '') }}">
                            @error('country')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <button type="submit" class="btn btn-primary">
                            {{ isset($location) ? 'Update' : 'Create' }} Location
                        </button>
                        <a href="{{ route('locations.index') }}" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection