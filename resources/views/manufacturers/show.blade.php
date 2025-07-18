@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h2>Manufacturer: {{ $manufacturer->name }}</h2>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5>Contact Information</h5>
                                </div>
                                <div class="card-body">
                                    <p><strong>Support Email:</strong> {{ $manufacturer->support_email ?? 'N/A' }}</p>
                                    <p><strong>Support Phone:</strong> {{ $manufacturer->support_phone ?? 'N/A' }}</p>
                                    <p><strong>Support URL:</strong> 
                                        @if($manufacturer->support_url)
                                            <a href="{{ $manufacturer->support_url }}" target="_blank">{{ $manufacturer->support_url }}</a>
                                        @else
                                            N/A
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5>Statistics</h5>
                                </div>
                                <div class="card-body">
                                    <p><strong>Models:</strong> {{ $manufacturer->models->count() }}</p>
                                    <p><strong>Assets:</strong> {{ $manufacturer->models->sum(function($model) { return $model->assets->count(); }) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <h4>Models by this Manufacturer</h4>
                    @if($manufacturer->models->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Model Name</th>
                                        <th>Category</th>
                                        <th>Assets</th>
                                        <th>Type</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($manufacturer->models as $model)
                                    <tr>
                                        <td>
                                            <a href="{{ route('asset-models.show', $model) }}">{{ $model->name }}</a>
                                        </td>
                                        <td>{{ $model->category->name }}</td>
                                        <td>{{ $model->assets->count() }}</td>
                                        <td>
                                            <span class="badge badge-{{ $model->is_consumable ? 'info' : 'primary' }}">
                                                {{ $model->is_consumable ? 'Consumable' : 'Fixed Asset' }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">No models found for this manufacturer.</div>
                    @endif
                </div>
                <div class="card-footer">
                    <a href="{{ route('manufacturers.edit', $manufacturer) }}" class="btn btn-primary">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <a href="{{ route('manufacturers.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to List
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection