@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h2>Model: {{ $model->name }}</h2>
                        <div>
                            <span class="badge badge-{{ $model->is_consumable ? 'info' : 'primary' }}">
                                {{ $model->is_consumable ? 'Consumable' : 'Fixed Asset' }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            @if($model->image)
                                <img src="{{ asset('storage/' . $model->image) }}" alt="Model Image" class="img-fluid mb-3">
                            @else
                                <div class="text-center py-4 bg-light">
                                    <i class="fas fa-image fa-5x text-muted"></i>
                                    <p class="mt-2">No Image Available</p>
                                </div>
                            @endif
                            
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h5>Details</h5>
                                </div>
                                <div class="card-body">
                                    <p><strong>Model Number:</strong> {{ $model->model_number ?? 'N/A' }}</p>
                                    <p><strong>Category:</strong> {{ $model->category->name }}</p>
                                    <p><strong>Manufacturer:</strong> {{ $model->manufacturer->name }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-8">
                            <h4>Assets</h4>
                            @if($model->assets->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th>Asset Tag</th>
                                                <th>Name</th>
                                                <th>Status</th>
                                                <th>Assigned To</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($model->assets as $asset)
                                            <tr>
                                                <td>{{ $asset->asset_tag }}</td>
                                                <td>{{ $asset->name }}</td>
                                                <td>
                                                    <span class="badge" style="background-color: {{ $asset->status->color }};">
                                                        {{ $asset->status->name }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if($asset->assigned_type == 'user' && $asset->assignedUser)
                                                        {{ $asset->assignedUser->name }}
                                                    @elseif($asset->assigned_type == 'department' && $asset->department)
                                                        {{ $asset->department->name }}
                                                    @else
                                                        Unassigned
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="{{ route('assets.show', $asset) }}" class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="alert alert-info">No assets found for this model.</div>
                            @endif
                            
                            @if($model->is_consumable)
                                <h4 class="mt-4">Consumable Inventory</h4>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>Quantity</th>
                                                <th>Min Quantity</th>
                                                <th>Purchase Cost</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($model->consumables as $consumable)
                                            <tr>
                                                <td>{{ $consumable->name }}</td>
                                                <td>{{ $consumable->quantity }}</td>
                                                <td>{{ $consumable->min_quantity }}</td>
                                                <td>{{ number_format($consumable->purchase_cost, 2) }}</td>
                                                <td>
                                                    <a href="{{ route('consumables.show', $consumable) }}" class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ route('asset-models.edit', $model) }}" class="btn btn-primary">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <a href="{{ route('asset-models.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to List
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection