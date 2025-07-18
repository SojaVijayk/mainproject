@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h2>Maintenance Record: {{ $record->title }}</h2>
                        <span class="badge badge-{{ 
                            $record->status == 'Scheduled' ? 'info' : 
                            ($record->status == 'In Progress' ? 'warning' : 
                            ($record->status == 'Completed' ? 'success' : 'secondary')) 
                        }}">
                            {{ $record->status }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5>Asset Information</h5>
                                </div>
                                <div class="card-body">
                                    <p><strong>Asset:</strong> 
                                        <a href="{{ route('assets.show', $record->asset) }}">
                                            {{ $record->asset->asset_tag }} - {{ $record->asset->name }}
                                        </a>
                                    </p>
                                    <p><strong>Model:</strong> {{ $record->asset->model->name }}</p>
                                    <p><strong>Assigned To:</strong> 
                                        @if($record->asset->assigned_type == 'user' && $record->asset->assignedUser)
                                            {{ $record->asset->assignedUser->name }}
                                        @elseif($record->asset->assigned_type == 'department' && $record->asset->department)
                                            {{ $record->asset->department->name }}
                                        @else
                                            Unassigned
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5>Maintenance Details</h5>
                                </div>
                                <div class="card-body">
                                    <p><strong>Created By:</strong> {{ $record->user->name }}</p>
                                    <p><strong>Start Date:</strong> {{ $record->start_date->format('Y-m-d') }}</p>
                                    <p><strong>Completion Date:</strong> 
                                        @if($record->completion_date)
                                            {{ $record->completion_date->format('Y-m-d') }}
                                        @else
                                            N/A
                                        @endif
                                    </p>
                                    <p><strong>Cost:</strong> 
                                        @if($record->cost)
                                            ${{ number_format($record->cost, 2) }}
                                        @else
                                            N/A
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5>Details</h5>
                        </div>
                        <div class="card-body">
                            {!! nl2br(e($record->details)) !!}
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ route('maintenance.edit', $record) }}" class="btn btn-primary">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <a href="{{ route('maintenance.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to List
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection