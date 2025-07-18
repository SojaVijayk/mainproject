@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h2>Department: {{ $department->name }}</h2>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5>Details</h5>
                                </div>
                                <div class="card-body">
                                    <p><strong>Manager:</strong> 
                                        @if($department->manager)
                                            {{ $department->manager->name }}
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
                                    <p><strong>Users:</strong> {{ $department->users->count() }}</p>
                                    <p><strong>Assets:</strong> {{ $department->assets->count() }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <h4>Users in this Department</h4>
                    @if($department->users->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Assets Assigned</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($department->users as $user)
                                    <tr>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>{{ $user->assignedAssets->count() }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">No users found in this department.</div>
                    @endif
                    
                    <h4 class="mt-4">Assets Assigned to this Department</h4>
                    @if($department->assets->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Asset Tag</th>
                                        <th>Name</th>
                                        <th>Model</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($department->assets as $asset)
                                    <tr>
                                        <td>{{ $asset->asset_tag }}</td>
                                        <td>{{ $asset->name }}</td>
                                        <td>{{ $asset->model->name }}</td>
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
                        <div class="alert alert-info">No assets assigned to this department.</div>
                    @endif
                </div>
                <div class="card-footer">
                    <a href="{{ route('departments.edit', $department) }}" class="btn btn-primary">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <a href="{{ route('departments.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to List
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection