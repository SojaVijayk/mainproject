@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-6">
            <h1>Asset Statuses</h1>
        </div>
        <div class="col-md-6 text-right">
            <a href="{{ route('asset-statuses.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add New Status
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Color</th>
                            <th>Assets</th>
                            <th>Default</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($statuses as $status)
                        <tr>
                            <td>{{ $status->name }}</td>
                            <td>
                                <span class="badge" style="background-color: {{ $status->color }}; color: {{ getContrastColor($status->color) }};">
                                    {{ $status->color }}
                                </span>
                            </td>
                            <td>{{ $status->assets->count() }}</td>
                            <td>
                                @if($status->is_default)
                                    <span class="badge badge-success">Yes</span>
                                @else
                                    <span class="badge badge-secondary">No</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('asset-statuses.show', $status) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('asset-statuses.edit', $status) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @if(!$status->is_default)
                                    <form action="{{ route('asset-statuses.destroy', $status) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection