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

<link rel="stylesheet" href="{{asset('assets/vendor/libs/chartjs/chartjs.css')}}" />


@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js')}}"></script>

<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/FormValidation.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/sweetalert2/sweetalert2.js')}}"></script>
<script src="{{asset('assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.js')}}"></script>
<script src="{{asset('assets/vendor/libs/block-ui/block-ui.js')}}"></script>
<script src="{{asset('assets/vendor/libs/chartjs/chartjs.js')}}"></script>

@endsection

@section('page-script')
<script>
    // Initialize charts
    document.addEventListener('DOMContentLoaded', function() {
        // Tapal Status Chart
        const statusCtx = document.getElementById('tapalStatusChart');
        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: ['Pending', 'Completed', 'Overdue'],
                datasets: [{
                    data: [{{ $stats['pending_count'] }}, {{ $stats['completed_count'] }}, {{ $stats['overdue_count'] }}],
                    backgroundColor: [
                        '#ffc107',
                        '#28a745',
                        '#dc3545'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    }
                }
            }
        });

        // Monthly Trend Chart
        const trendCtx = document.getElementById('tapalTrendChart');
        new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($stats['monthly_labels']) !!},
                datasets: [{
                    label: 'Tapals Received',
                    data: {!! json_encode($stats['monthly_data']) !!},
                    backgroundColor: 'rgba(40, 167, 69, 0.2)',
                    borderColor: 'rgba(40, 167, 69, 1)',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    });
</script>
@endsection

@section('content')
<div class="row mb-4">
 <!-- Statistics Cards -->
    <div class="col-md-6 col-lg-3 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="fw-semibold d-block mb-1">Today's Tapals</span>
                        <h3 class="card-title mb-2">{{ $stats['today_count'] }}</h3>
                        <small class="text-success fw-semibold">{{ $stats['today_completed'] }} completed</small>
                    </div>
                    <div class="avatar">
                        <span class="avatar-initial rounded bg-label-primary">
                            <i class="bx bx-file"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-3 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="fw-semibold d-block mb-1">This Month</span>
                        <h3 class="card-title mb-2">{{ $stats['month_count'] }}</h3>
                        <small class="text-success fw-semibold">{{ $stats['month_completed'] }} completed</small>
                    </div>
                    <div class="avatar">
                        <span class="avatar-initial rounded bg-label-info">
                            <i class="bx bx-calendar"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-3 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="fw-semibold d-block mb-1">This Year</span>
                        <h3 class="card-title mb-2">{{ $stats['year_count'] }}</h3>
                        <small class="text-success fw-semibold">{{ $stats['year_completed'] }} completed</small>
                    </div>
                    <div class="avatar">
                        <span class="avatar-initial rounded bg-label-success">
                            <i class="bx bx-stats"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-3 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="fw-semibold d-block mb-1">Pending Tapals</span>
                        <h3 class="card-title mb-2">{{ $stats['pending_count'] }}</h3>
                        <small class="text-danger fw-semibold">{{ $stats['overdue_count'] }} overdue</small>
                    </div>
                    <div class="avatar">
                        <span class="avatar-initial rounded bg-label-warning">
                            <i class="bx bx-time"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <!-- Charts Section -->
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between">
                <h5 class="mb-0">Tapal Status</h5>
            </div>
            <div class="card-body">
                <canvas id="tapalStatusChart" class="chartjs" height="250"></canvas>
            </div>
        </div>
    </div>

    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between">
                <h5 class="mb-0">Monthly Trend</h5>
            </div>
            <div class="card-body">
                <canvas id="tapalTrendChart" class="chartjs" height="250"></canvas>
            </div>
        </div>
    </div>
</div>

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
                            $lastMovement = $tapal->movements->where('is_assignment',1)->last();
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
                        @if($tapal->created_by == Auth::id() && $lastMovement->status != 'Accepted' && $lastMovement->status!= 'Completed')
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
