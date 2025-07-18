@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-6">
            <h1>Manufacturers</h1>
        </div>
        <div class="col-md-6 text-right">
            <a href="{{ route('manufacturers.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add New Manufacturer
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
                            <th>Support Email</th>
                            <th>Support Phone</th>
                            <th>Models</th>
                            <th>Assets</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($manufacturers as $manufacturer)
                        <tr>
                            <td>{{ $manufacturer->name }}</td>
                            <td>{{ $manufacturer->support_email ?? 'N/A' }}</td>
                            <td>{{ $manufacturer->support_phone ?? 'N/A' }}</td>
                            <td>{{ $manufacturer->models->count() }}</td>
                            <td>{{ $manufacturer->models->sum(function($model) { return $model->assets->count(); }) }}</td>
                            <td>
                                <a href="{{ route('manufacturers.show', $manufacturer) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('manufacturers.edit', $manufacturer) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('manufacturers.destroy', $manufacturer) }}" method="POST" class="d-inline">
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
            
            {{ $manufacturers->links() }}
        </div>
    </div>
</div>
@endsection