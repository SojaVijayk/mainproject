@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h2>Consumable: {{ $consumable->name }}</h2>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5>Details</h5>
                                </div>
                                <div class="card-body">
                                    <p><strong>Category:</strong> {{ $consumable->category->name }}</p>
                                    <p><strong>Supplier:</strong> {{ $consumable->supplier->name }}</p>
                                    <p><strong>Purchase Date:</strong> {{ $consumable->purchase_date->format('Y-m-d') }}</p>
                                    <p><strong>Purchase Cost:</strong> ${{ number_format($consumable->purchase_cost, 2) }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5>Inventory</h5>
                                </div>
                                <div class="card-body">
                                    <p><strong>Current Quantity:</strong> {{ $consumable->quantity }}</p>
                                    <p><strong>Minimum Quantity:</strong> {{ $consumable->min_quantity }}</p>
                                    <p><strong>Available:</strong> {{ $consumable->quantity - $consumable->assignments->sum('quantity') }}</p>
                                    @if($consumable->quantity <= $consumable->min_quantity)
                                        <div class="alert alert-warning mt-2">
                                            <strong>Low Stock!</strong> Quantity is at or below minimum level.
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <h4>Checkout History</h4>
                    @if($consumable->assignments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Quantity</th>
                                        <th>Assigned To</th>
                                        <th>Floor/Location</th>
                                        <th>Notes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($consumable->assignments as $assignment)
                                    <tr>
                                        <td>{{ $assignment->created_at->format('Y-m-d') }}</td>
                                        <td>{{ $assignment->quantity }}</td>
                                        <td>
                                            @if($assignment->user)
                                                {{ $assignment->user->name }}
                                            @elseif($assignment->department)
                                                {{ $assignment->department->name }} (Department)
                                            @endif
                                        </td>
                                        <td>{{ $assignment->floor ?? 'N/A' }}</td>
                                        <td>{{ $assignment->notes ?? 'N/A' }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">No checkout history found for this consumable.</div>
                    @endif
                </div>
                <div class="card-footer">
                    <a href="{{ route('consumables.edit', $consumable) }}" class="btn btn-primary">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <a href="{{ route('consumables.checkout', $consumable) }}" class="btn btn-success">
                        <i class="fas fa-arrow-right"></i> Checkout
                    </a>
                    <a href="{{ route('consumables.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to List
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection