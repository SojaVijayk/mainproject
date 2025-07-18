@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h2>{{ isset($model) ? 'Edit' : 'Create' }} Asset Model</h2>
                </div>
                <div class="card-body">
                    <form action="{{ isset($model) ? route('asset-models.update', $model) : route('asset-models.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @if(isset($model))
                            @method('PUT')
                        @endif
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Name *</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" 
                                           value="{{ old('name', $model->name ?? '') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="form-group">
                                    <label for="model_number">Model Number</label>
                                    <input type="text" class="form-control @error('model_number') is-invalid @enderror" id="model_number" 
                                           name="model_number" value="{{ old('model_number', $model->model_number ?? '') }}">
                                    @error('model_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="form-group">
                                    <label for="category_id">Category *</label>
                                    <select class="form-control @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
                                        <option value="">Select Category</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" 
                                                {{ old('category_id', $model->category_id ?? '') == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="form-group">
                                    <label for="manufacturer_id">Manufacturer *</label>
                                    <select class="form-control @error('manufacturer_id') is-invalid @enderror" id="manufacturer_id" name="manufacturer_id" required>
                                        <option value="">Select Manufacturer</option>
                                        @foreach($manufacturers as $manufacturer)
                                            <option value="{{ $manufacturer->id }}" 
                                                {{ old('manufacturer_id', $model->manufacturer_id ?? '') == $manufacturer->id ? 'selected' : '' }}>
                                                {{ $manufacturer->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('manufacturer_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="image">Model Image</label>
                                    <input type="file" class="form-control-file @error('image') is-invalid @enderror" id="image" name="image">
                                    @error('image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    
                                    @if(isset($model) && $model->image)
                                        <div class="mt-2">
                                            <img src="{{ asset('storage/' . $model->image) }}" alt="Model Image" class="img-thumbnail" style="max-height: 150px;">
                                            <div class="form-check mt-2">
                                                <input class="form-check-input" type="checkbox" id="remove_image" name="remove_image">
                                                <label class="form-check-label" for="remove_image">
                                                    Remove current image
                                                </label>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                
                                <div class="form-group">
                                    <label for="is_consumable">Type</label>
                                    <select class="form-control @error('is_consumable') is-invalid @enderror" id="is_consumable" name="is_consumable">
                                        <option value="0" {{ old('is_consumable', $model->is_consumable ?? 0) == 0 ? 'selected' : '' }}>Fixed Asset</option>
                                        <option value="1" {{ old('is_consumable', $model->is_consumable ?? 0) == 1 ? 'selected' : '' }}>Consumable</option>
                                    </select>
                                    @error('is_consumable')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">
                            {{ isset($model) ? 'Update' : 'Create' }} Model
                        </button>
                        <a href="{{ route('asset-models.index') }}" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection