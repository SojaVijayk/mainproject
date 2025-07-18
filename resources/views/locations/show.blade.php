@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h2>Location: {{ $location->name }}</h2>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5>Address Information</h5>
                                </div>
                                <div class="card-body">
                                    <p><strong>Address:</strong> {{ $location->address ?? 'N/A' }}</p>
                                    <p><strong>City:</strong> {{ $location->city ?? 'N/A' }}</p>
                                    <p><strong>State/Province:</strong> {{ $location->state ?? 'N/A' }}</p>
                                    <p><strong>Zip/Postal Code:</strong> {{ $location->zip_code ?? 'N/A' }}</p>
                                    <p><strong>Country:</strong> {{ $location->country ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5>Statistics</h5>
                                </div>
                                <div class="card-body">
                                    <p><strong>Assets:</strong> {{ $location->assets->count() }}</p>
                                    <p><strong>Users:</strong> {{ $location->users->count() }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <h4>Assets at this Location</h4>
                    @if($location->assets->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Asset Tag</th>
                                        <th>Name</th>
                                        <th>Model</th>
                                        <th>Status</th>
                                        <th>Assigned To</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($location->assets as $asset)
                                    <tr>
                                        <td>{{ $asset->asset_tag }}</td>
                                        <td>{{ $asset->name }}</td>
                                        <td>{{ $asset->model->name }}</td>
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
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">No assets found at this location.</div>
                    @endif
                </div>
                <div class="card-footer">
                    <a href="{{ route('locations.edit', $location) }}" class="btn btn-primary">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <a href="{{ route('locations.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to List
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection