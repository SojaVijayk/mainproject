@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h2>Category: {{ $category->name }}</h2>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <p><strong>Description:</strong> {{ $category->description ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Statistics</h5>
                                </div>
                                <div class="card-body">
                                    <p><strong>Models:</strong> {{ $category->models->count() }}</p>
                                    <p><strong>Assets:</strong> {{ $category->models->sum(function($model) { return $model->assets->count(); }) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <h4 class="mt-4">Models in this Category</h4>
                    @if($category->models->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Model Name</th>
                                        <th>Manufacturer</th>
                                        <th>Assets</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($category->models as $model)
                                    <tr>
                                        <td>
                                            <a href="{{ route('asset-models.show', $model) }}">{{ $model->name }}</a>
                                        </td>
                                        <td>{{ $model->manufacturer->name }}</td>
                                        <td>{{ $model->assets->count() }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">No models found in this category.</div>
                    @endif
                </div>
                <div class="card-footer">
                    <a href="{{ route('asset-categories.edit', $category) }}" class="btn btn-primary">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <a href="{{ route('asset-categories.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to List
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection