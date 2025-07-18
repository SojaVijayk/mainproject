@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h2>Status: {{ $status->name }}</h2>
                        <span class="badge" style="background-color: {{ $status->color }}; color: {{ getContrastColor($status->color) }};">
                            {{ $status->color }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <p><strong>Notes:</strong> {{ $status->notes ?? 'N/A' }}</p>
                            <p><strong>Default Status:</strong> 
                                @if($status->is_default)
                                    <span class="badge badge-success">Yes</span>
                                @else
                                    <span class="badge badge-secondary">No</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Statistics</h5>
                                </div>
                                <div class="card-body">
                                    <p><strong>Assets:</strong> {{ $status->assets->count() }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <h4 class="mt-4">Assets with this Status</h4>
                    @if($status->assets->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Asset Tag</th>
                                        <th>Name</th>
                                        <th>Model</th>
                                        <th>Assigned To</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($status->assets as $asset)
                                    <tr>
                                        <td>{{ $asset->asset_tag }}</td>
                                        <td>{{ $asset->name }}</td>
                                        <td>{{ $asset->model->name }}</td>
                                        <td>
                                            @if($asset->assigned_type == 'user' && $asset->assignedUser)
                                                {{ $asset->assignedUser->name }}
                                            @elseif($asset->assigned_type == 'department' && $asset->department)
                                                {{ $asset->department->name }}
                                            @else
                                                Unassigned
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">No assets found with this status.</div>
                    @endif
                </div>
                <div class="card-footer">
                    <a href="{{ route('asset-statuses.edit', $status) }}" class="btn btn-primary">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <a href="{{ route('asset-statuses.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to List
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection