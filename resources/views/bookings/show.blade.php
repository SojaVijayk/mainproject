@extends('layouts/layoutMaster')

@section('title', 'Booking Details')
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
<script src="{{asset('assets/vendor/libs/chartjs/chartjs.js')}}"></script>

@endsection

@section('page-script')

@endsection

@section('content')
<div class="container">
  <div class="row mb-4">
    <div class="col-md-6">
      <h1>Booking Details</h1>
    </div>
    <div class="col-md-6 text-end">
      @if($booking->status === 'booking_progress')
      <a href="{{ route('bookings.edit', $booking) }}" class="btn btn-primary me-2">Edit</a>
      <form action="{{ route('bookings.confirm', $booking) }}" method="POST" class="d-inline">
        @csrf
        <button type="submit" class="btn btn-success">Confirm Booking</button>
      </form>
      @endif
      <a href="{{ route('bookings.downloadPdf', $booking) }}" class="btn btn-secondary">Download PDF</a>
    </div>
  </div>

  <div class="card mb-4">
    <div class="card-header">
      <h5>Booking Information</h5>
    </div>
    <div class="card-body">
      <div class="row">
        <div class="col-md-6">
          <table class="table table-bordered">
            <tr>
              <th>Booking Number</th>
              <td>{{ $booking->booking_number ?? 'N/A' }}</td>
            </tr>
            <tr>
              <th>Status</th>
              <td>
                <span class="badge
                                    @if($booking->status === 'confirmed') bg-success
                                    @elseif($booking->status === 'cancelled') bg-danger
                                    @else bg-warning text-dark @endif">
                  {{ ucfirst($booking->status) }}
                </span>
              </td>
            </tr>
            <tr>
              <th>Event Name</th>
              <td>{{ $booking->event_name }}</td>
            </tr>
            <tr>
              <th>Event Type</th>
              <td>{{ ucfirst($booking->event_type) }}</td>
            </tr>
            <tr>
              <th>Event Mode</th>
              <td>{{ ucfirst($booking->event_mode) }}</td>
            </tr>
          </table>
        </div>
        <div class="col-md-6">
          <table class="table table-bordered">
            <tr>
              <th>Hosted By</th>
              <td>{{ $booking->hostedBy->name }}</td>
            </tr>
            @if($booking->external_organization)
            <tr>
              <th>External Organization</th>
              <td>{{ $booking->external_organization }}</td>
            </tr>
            @endif
            <tr>
              <th>Coordinator</th>
              <td>{{ $booking->coordinator->name }}</td>
            </tr>
            <tr>
              <th>Participants</th>
              <td>{{ $booking->participants_count }}</td>
            </tr>
          </table>
        </div>
      </div>

      <div class="row mt-3">
        <div class="col-md-6">
          <table class="table table-bordered">
            <tr>
              <th>Date/Time</th>
              <td>
                {{ $booking->start_time->format('M d, Y h:i A') }}<br>
                to {{ $booking->end_time->format('M d, Y h:i A') }}
              </td>
            </tr>
            <tr>
              <th>Seat Layout</th>
              <td>
                {{ str_replace('_', ' ', ucfirst($booking->seat_layout)) }}
                @if($booking->custom_seat_layout)
                ({{ $booking->custom_seat_layout }})
                @endif
              </td>
            </tr>
          </table>
        </div>
        <div class="col-md-6">
          <table class="table table-bordered">
            <tr>
              <th>Venues</th>
              <td>
                @foreach($booking->venues as $venue)
                {{ $venue->name }}@if(!$loop->last), @endif
                @endforeach
              </td>
            </tr>
            <tr>
              <th>Amenities</th>
              <td>
                @if($booking->amenities)
                {{ implode(', ', $booking->amenities) }}
                @else
                None
                @endif
              </td>
            </tr>
          </table>
        </div>
      </div>

      @if($booking->additional_requirements)
      <div class="mt-3">
        <h6>Additional Requirements</h6>
        <p>{{ $booking->additional_requirements }}</p>
      </div>
      @endif
    </div>
  </div>

  <div class="card mb-4">
    <div class="card-header">
      <h5>Venue Calendar</h5>
    </div>
    <div class="card-body">
      <div id="calendar"></div>
    </div>
  </div>

  @if($booking->status === 'confirmed')
  <div class="card">
    <div class="card-header">
      <h5>Time Change Requests</h5>
    </div>
    <div class="card-body">
      @if(auth()->user()->id === $booking->coordinator_id || auth()->user()->id === $booking->hosted_by)
      <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#requestModal">
        Request Time Change
      </button>

      @if($booking->requests->count() > 0)
      <div class="table-responsive">
        <table class="table table-striped">
          <thead>
            <tr>
              <th>Requested By</th>
              <th>Message</th>
              <th>Status</th>
              <th>Response</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            @foreach($booking->requests as $request)
            <tr>
              <td>{{ $request->requestedBy->name }}</td>
              <td>{{ $request->message }}</td>
              <td>
                <span class="badge
                                        @if($request->status === 'approved') bg-success
                                        @elseif($request->status === 'rejected') bg-danger
                                        @else bg-warning text-dark @endif">
                  {{ ucfirst($request->status) }}
                </span>
              </td>
              <td>{{ $request->response ?? 'N/A' }}</td>
              <td>
                @if($request->status === 'pending' && (auth()->user()->id === $booking->coordinator_id ||
                auth()->user()->id === $booking->hosted_by))
                <button class="btn btn-sm btn-success respond-btn" data-bs-toggle="modal" data-bs-target="#respondModal"
                  data-request-id="{{ $request->id }}" data-status="approved">
                  Approve
                </button>
                <button class="btn btn-sm btn-danger respond-btn" data-bs-toggle="modal" data-bs-target="#respondModal"
                  data-request-id="{{ $request->id }}" data-status="rejected">
                  Reject
                </button>
                @endif
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      @else
      <p>No time change requests found.</p>
      @endif
      @else
      <p>Only the booking coordinator or host can manage time change requests.</p>
      @endif
    </div>
  </div>
  @endif
</div>

<!-- Request Time Change Modal -->
<div class="modal fade" id="requestModal" tabindex="-1" aria-labelledby="requestModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="{{ route('bookings.requestTimeChange', $booking) }}" method="POST">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title" id="requestModalLabel">Request Time Change</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="message" class="form-label">Message</label>
            <textarea class="form-control" id="message" name="message" rows="3" required></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Submit Request</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Respond to Request Modal -->
<div class="modal fade" id="respondModal" tabindex="-1" aria-labelledby="respondModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="respondForm" method="POST">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title" id="respondModalLabel">Respond to Request</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="status" id="responseStatus">
          <div class="mb-3">
            <label for="response" class="form-label">Response</label>
            <textarea class="form-control" id="response" name="response" rows="3" required></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Submit Response</button>
        </div>
      </form>
    </div>
  </div>
</div>

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            events: {
                url: '{{ route("events.calendar") }}',
                method: 'GET'
            },
            eventDidMount: function(info) {
                // Highlight the current booking
                if (info.event.id == '{{ $booking->id }}') {
                    info.el.style.border = '3px solid #28a745';
                }

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

        // Set up respond buttons
        $('.respond-btn').click(function() {
            const requestId = $(this).data('request-id');
            const status = $(this).data('status');

            $('#responseStatus').val(status);
            $('#respondForm').attr('action', '/booking-requests/' + requestId + '/respond');
        });
    });
</script>
@endpush
@endsection