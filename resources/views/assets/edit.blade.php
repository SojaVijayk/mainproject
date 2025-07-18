@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col">
            <h2>Edit Asset: {{ $asset->asset_tag }}</h2>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('assets.update', $asset->id) }}" method="POST">
                @csrf
                @method('PUT')
                @include('assets._form', ['buttonText' => 'Update Asset'])
            </form>
        </div>
    </div>
</div>
@endsection