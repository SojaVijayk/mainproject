@extends('layouts/layoutMaster')

@section('title', 'Leave - Request')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/formvalidation/dist/css/formValidation.min.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/animate-css/animate.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/sweetalert2/sweetalert2.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/spinkit/spinkit.css')}}" />


@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js')}}"></script>

<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/FormValidation.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/sweetalert2/sweetalert2.js')}}"></script>
<script src="{{asset('assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.js')}}"></script>
<script src="{{asset('assets/vendor/libs/block-ui/block-ui.js')}}"></script>


@endsection

@section('page-script')
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">My Tapals</h3>
        <div class="card-tools">
          @if((auth()->user()->can('tapals.create')) || Auth::id()== 20 || Auth::id() == 1)
            <a href="{{ route('tapals.create') }}" class="btn btn-primary">Create New Tapal</a>
            @endif
        </div>
    </div>
    <div class="card-body">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Tapal No.</th>
                    <th>Subject</th>
                    <th>Type</th>
                    <th>Created On</th>
                    <th>Current Holder</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tapals as $tapal)
                <tr>
                    <td>{{ $tapal->tapal_number }}</td>
                    <td>{{ $tapal->subject }}</td>
                    <td>{{ ucfirst($tapal->type) }}</td>
                    <td>{{ $tapal->created_at->format('d-M-Y') }}</td>
                    <td>{{ $tapal->currentHolder ? $tapal->currentHolder->name : 'N/A' }}</td>
                    <td>
                        @php
                            $lastMovement = $tapal->movements->last();
                        @endphp
                        @if($lastMovement)
                            {{ $lastMovement->status }}
                            @if($lastMovement->custom_status)
                                ({{ $lastMovement->custom_status }})
                            @endif
                        @else
                            New
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('tapals.show', $tapal->id) }}" class="btn btn-sm btn-success">View</a>
                        @if($tapal->created_by == Auth::id())
                            <a href="{{ route('tapals.edit', $tapal->id) }}" class="btn btn-sm btn-primary">Edit</a>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
