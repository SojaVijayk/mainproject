@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-6">
            <h1>Consumables</h1>
        </div>
        <div class="col-md-6 text-right">
            <a href="{{ route('consumables.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add New Consumable
            </a>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-filter"></i> Filters
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('consumables.index') }}">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="search">Search</label>
                            <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Name, category...">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="category_id">Category</label>
                            <select class="form-control" name="category_id">
                                <option value="">All Categories</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="supplier_id">Supplier</label>
                            <select class="form-control" name="supplier_id">
                                <option value="">All Suppliers</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                        {{ $supplier->name }}
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
                    <a href="{{ route('consumables.index') }}" class="btn btn-secondary">
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
                            <th>Name</th>
                            <th>Category</th>
                            <th>Supplier</th>
                            <th>Quantity</th>
                            <th>Min Qty</th>
                            <th>Purchase Cost</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($consumables as $consumable)
                        <tr class="{{ $consumable->quantity <= $consumable->min_quantity ? 'table-warning' : '' }}">
                            <td>{{ $consumable->name }}</td>
                            <td>{{ $consumable->category->name }}</td>
                            <td>{{ $consumable->supplier->name }}</td>
                            <td>
                                {{ $consumable->quantity }}
                                @if($consumable->quantity <= $consumable->min_quantity)
                                    <span class="badge badge-danger">Low Stock</span>
                                @endif
                            </td>
                            <td>{{ $consumable->min_quantity }}</td>
                            <td>${{ number_format($consumable->purchase_cost, 2) }}</td>
                            <td>
                                <a href="{{ route('consumables.show', $consumable) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('consumables.edit', $consumable) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="{{ route('consumables.checkout', $consumable) }}" class="btn btn-sm btn-success">
                                    <i class="fas fa-arrow-right"></i> Checkout
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            {{ $consumables->links() }}
        </div>
    </div>
</div>
@endsection