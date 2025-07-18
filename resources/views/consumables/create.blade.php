@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h2>{{ isset($consumable) ? 'Edit' : 'Create' }} Consumable</h2>
                </div>
                <div class="card-body">
                    <form action="{{ isset($consumable) ? route('consumables.update', $consumable) : route('consumables.store') }}" method="POST">
                        @csrf
                        @if(isset($consumable))
                            @method('PUT')
                        @endif
                        
                        <div class="form-group">
                            <label for="name">Name *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" 
                                   value="{{ old('name', $consumable->name ?? '') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="category_id">Category *</label>
                            <select class="form-control @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" 
                                        {{ old('category_id', $consumable->category_id ?? '') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="supplier_id">Supplier *</label>
                            <select class="form-control @error('supplier_id') is-invalid @enderror" id="supplier_id" name="supplier_id" required>
                                <option value="">Select Supplier</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" 
                                        {{ old('supplier_id', $consumable->supplier_id ?? '') == $supplier->id ? 'selected' : '' }}>
                                        {{ $supplier->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('supplier_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="quantity">Quantity *</label>
                                    <input type="number" class="form-control @error('quantity') is-invalid @enderror" id="quantity" 
                                           name="quantity" value="{{ old('quantity', $consumable->quantity ?? 0) }}" min="0" required>
                                    @error('quantity')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="min_quantity">Minimum Quantity *</label>
                                    <input type="number" class="form-control @error('min_quantity') is-invalid @enderror" id="min_quantity" 
                                           name="min_quantity" value="{{ old('min_quantity', $consumable->min_quantity ?? 0) }}" min="0" required>
                                    @error('min_quantity')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="purchase_cost">Purchase Cost *</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">$</span>
                                        </div>
                                        <input type="number" step="0.01" class="form-control @error('purchase_cost') is-invalid @enderror" 
                                               id="purchase_cost" name="purchase_cost" 
                                               value="{{ old('purchase_cost', $consumable->purchase_cost ?? 0) }}" min="0" required>
                                        @error('purchase_cost')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="purchase_date">Purchase Date *</label>
                            <input type="date" class="form-control @error('purchase_date') is-invalid @enderror" id="purchase_date" 
                                   name="purchase_date" value="{{ old('purchase_date', isset($consumable) ? $consumable->purchase_date->format('Y-m-d') : '') }}" required>
                            @error('purchase_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <button type="submit" class="btn btn-primary">
                            {{ isset($consumable) ? 'Update' : 'Create' }} Consumable
                        </button>
                        <a href="{{ route('consumables.index') }}" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection