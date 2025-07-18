@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h2>Supplier: {{ $supplier->name }}</h2>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5>Contact Information</h5>
                                </div>
                                <div class="card-body">
                                    <p><strong>Contact Person:</strong> {{ $supplier->contact_person ?? 'N/A' }}</p>
                                    <p><strong>Email:</strong> {{ $supplier->email ?? 'N/A' }}</p>
                                    <p><strong>Phone:</strong> {{ $supplier->phone ?? 'N/A' }}</p>
                                    <p><strong>Address:</strong> {{ $supplier->address ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5>Statistics</h5>
                                </div>
                                <div class="card-body">
                                    <p><strong>Assets Purchased:</strong> {{ $supplier->assets->count() }}</p>
                                    <p><strong>Consumables Purchased:</strong> {{ $supplier->consumables->count() }}</p>
                                    <p><strong>Total Purchases:</strong> 
                                        ${{ number_format($supplier->assets->sum('purchase_cost') + $supplier->consumables->sum(function($c) { return $c->purchase_cost * $c->quantity; }), 2) }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <h4>Assets from this Supplier</h4>
                    @if($supplier->assets->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Asset Tag</th>
                                        <th>Name</th>
                                        <th>Model</th>
                                        <th>Purchase Date</th>
                                        <th>Purchase Cost</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($supplier->assets as $asset)
                                    <tr>
                                        <td>{{ $asset->asset_tag }}</td>
                                        <td>{{ $asset->name }}</td>
                                        <td>{{ $asset->model->name }}</td>
                                        <td>{{ $asset->purchase_date->format('Y-m-d') }}</td>
                                        <td>${{ number_format($asset->purchase_cost, 2) }}</td>
                                        <td>
                                            <span class="badge" style="background-color: {{ $asset->status->color }};">
                                                {{ $asset->status->name }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">No assets found from this supplier.</div>
                    @endif
                    
                    <h4 class="mt-4">Consumables from this Supplier</h4>
                    @if($supplier->consumables->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Category</th>
                                        <th>Quantity</th>
                                        <th>Purchase Date</th>
                                        <th>Total Cost</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($supplier->consumables as $consumable)
                                    <tr>
                                        <td>{{ $consumable->name }}</td>
                                        <td>{{ $consumable->category->name }}</td>
                                        <td>{{ $consumable->quantity }}</td>
                                        <td>{{ $consumable->purchase_date->format('Y-m-d') }}</td>
                                        <td>${{ number_format($consumable->purchase_cost * $consumable->quantity, 2) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">No consumables found from this supplier.</div>
                    @endif
                </div>
                <div class="card-footer">
                    <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-primary">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to List
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection