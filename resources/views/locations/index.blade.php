@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-6">
            <h1>Locations</h1>
        </div>
        <div class="col-md-6 text-right">
            <a href="{{ route('locations.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add New Location
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
                            <th>Address</th>
                            <th>City</th>
                            <th>Country</th>
                            <th>Assets</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($locations as $location)
                        <tr>
                            <td>{{ $location->name }}</td>
                            <td>{{ Str::limit($location->address, 30) ?? 'N/A' }}</td>
                            <td>{{ $location->city ?? 'N/A' }}</td>
                            <td>{{ $location->country ?? 'N/A' }}</td>
                            <td>{{ $location->assets->count() }}</td>
                            <td>
                                <a href="{{ route('locations.show', $location) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('locations.edit', $location) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('locations.destroy', $location) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            {{ $locations->links() }}
        </div>
    </div>
</div>
@endsection