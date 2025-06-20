@extends('layouts/layoutMaster')

@section('title', 'Bookings')
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

@section('page-style')

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
<script src='https://cdn.jsdelivr.net/npm/fullcalendar-scheduler@6.1.15/index.global.min.js'></script>
<script>
  document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'resourceTimelineWeek',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'resourceTimelineDay,resourceTimelineWeek'
            },
             resources: {
            url: '{{ route("events.calendar") }}',
            method: 'GET',
            extraParams: {
                isResources: true
            }
        },
            events: {
                url: '{{ route("events.calendar") }}',
                method: 'GET'
            },
            eventDidMount: function(info) {
                // Add tooltip
                $(info.el).tooltip({
                    title: `
                        <strong>${info.event.title}</strong><br>
                        Type: ${info.event.extendedProps.event_type}<br>
                        Host: ${info.event.extendedProps.hosted_by}<br>
                        Participants: ${info.event.extendedProps.participants}<br>
                        Status: ${info.event.extendedProps.status}
                    `,
                    html: true,
                    placement: 'top'
                });
            }
        });
        calendar.render();
    });
</script>

@endsection


@section('content')
<div class="container">
  <div class="row mb-4">
    <div class="col-md-6">
      <h1>Bookings</h1>
    </div>
    <div class="col-md-6 text-end">
      <a href="{{ route('bookings.create') }}" class="btn btn-primary">New Booking</a>
    </div>
  </div>

  <div class="card mb-4">
    <div class="card-header">
      <h5>Booking Calendar</h5>
    </div>
    <div class="card-body">
      <div id="calendar"></div>
    </div>
  </div>

  <div class="card">
    <div class="card-header">
      <h5>Booking List</h5>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-striped">
          <thead>
            <tr>
              <th>Booking #</th>
              <th>Event Name</th>
              <th>Venues</th>
              <th>Date/Time</th>
              <th>Host</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            @foreach($bookings as $booking)
            <tr>
              <td>{{ $booking->booking_number ?? 'In Progress' }}</td>
              <td>{{ $booking->event_name }}</td>
              <td>
                @foreach($booking->venues as $venue)
                {{ $venue->name }}@if(!$loop->last), @endif
                @endforeach
              </td>
              <td>
                {{ $booking->start_time->format('M d, Y h:i A') }}<br>
                to {{ $booking->end_time->format('M d, Y h:i A') }}
              </td>
              <td>{{ $booking->hostedBy->name }}</td>
              <td>
                <span class="badge
                                    @if($booking->status === 'confirmed') bg-success
                                    @elseif($booking->status === 'cancelled') bg-danger
                                    @else bg-warning text-dark @endif">
                  {{ ucfirst($booking->status) }}
                </span>
              </td>
              <td>
                <a href="{{ route('bookings.show', $booking) }}" class="btn btn-sm btn-info">View</a>
                @if($booking->status === 'booking_progress')
                <a href="{{ route('bookings.edit', $booking) }}" class="btn btn-sm btn-primary">Edit</a>
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