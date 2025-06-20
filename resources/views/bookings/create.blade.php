@extends('layouts/layoutMaster')

@section('title', 'Create Booking')
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
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar-scheduler@6.1.15/index.global.min.js'></script>
<script>
  $(document).ready(function() {
        // Show/hide external organization field based on hosted_by selection
        $('#hosted_by').change(function() {
            const selectedOption = $(this).find('option:selected').text();
            if (selectedOption.toLowerCase().includes('external')) {
                $('#externalOrgField').show();
                $('#external_organization').prop('required', true);
            } else {
                $('#externalOrgField').hide();
                $('#external_organization').prop('required', false);
            }
        });

        // Show/hide custom seat layout field
        $('#seat_layout').change(function() {
            if ($(this).val() === 'custom') {
                $('#customSeatLayoutField').show();
                $('#custom_seat_layout').prop('required', true);
            } else {
                $('#customSeatLayoutField').hide();
                $('#custom_seat_layout').prop('required', false);
            }
        });

        // Show/hide venue sections based on venue type selection
        $('input[name="venue_type"]').change(function() {
            const selectedType = $(this).val();

            if (selectedType === 'inhouse') {
                $('#inhouseVenuesSection').show();
                $('#externalVenuesSection').hide();
            } else if (selectedType === 'external') {
                $('#inhouseVenuesSection').hide();
                $('#externalVenuesSection').show();
            } else if (selectedType === 'both') {
                $('#inhouseVenuesSection').show();
                $('#externalVenuesSection').show();
            }
        });

        // Add external venue fields
        let externalVenueCounter = 1;
        $('#addExternalVenue').click(function() {
            const newVenue = `
                <div class="external-venue mb-3">
                    <div class="row">
                        <div class="col-md-4">
                            <input type="text" class="form-control" name="external_venues[${externalVenueCounter}][name]" placeholder="Venue Name">
                        </div>
                        <div class="col-md-4">
                            <input type="number" class="form-control" name="external_venues[${externalVenueCounter}][capacity]" placeholder="Capacity">
                        </div>
                        <div class="col-md-4">
                            <input type="text" class="form-control" name="external_venues[${externalVenueCounter}][details]" placeholder="Details">
                        </div>
                    </div>
                    <button type="button" class="btn btn-sm btn-danger remove-external-venue mt-2">Remove</button>
                </div>
            `;
            $('#externalVenuesContainer').append(newVenue);
            externalVenueCounter++;
        });

        // Remove external venue fields
        $(document).on('click', '.remove-external-venue', function() {
            $(this).closest('.external-venue').remove();
        });

        // Initialize calendar when required fields are filled
        const requiredFields = ['event_name', 'event_type', 'event_mode', 'hosted_by', 'coordinator_id', 'participants_count', 'start_time', 'end_time'];

        function checkRequiredFields() {
            let allFilled = true;
            requiredFields.forEach(field => {
                const value = $(`#${field}`).val();
                if (!value) {
                    allFilled = false;
                    return false;
                }
            });

            if (allFilled) {
                $('#calendarSection').show();
                initializeCalendar();
            } else {
                $('#calendarSection').hide();
            }

            // Enable/disable submit button based on venue selection
            const inhouseSelected = $('input[name="inhouse_venues[]"]:checked').length > 0;
            const externalSelected = $('input[name^="external_venues["][name$="[name]"]').filter(function() {
                return $(this).val() !== '';
            }).length > 0;

            const venueType = $('input[name="venue_type"]:checked').val();

            let venueValid = false;
            if (venueType === 'inhouse') {
                venueValid = inhouseSelected;
            } else if (venueType === 'external') {
                venueValid = externalSelected;
            } else if (venueType === 'both') {
                venueValid = inhouseSelected || externalSelected;
            }

            $('#submitBookingBtn').prop('disabled', !(allFilled && venueValid));
        }

        requiredFields.forEach(field => {
            $(`#${field}`).on('change input', checkRequiredFields);
        });

        $('input[name="venue_type"], input[name="inhouse_venues[]"], input[name^="external_venues["]').change(checkRequiredFields);


        // Initialize FullCalendar
        let calendar;
        let selectedEvent = null;

       function initializeCalendar() {
   const startTime = $('#start_time').val();
    const endTime = $('#end_time').val();

    if (!startTime || !endTime) return;

    const startDate = new Date(startTime);
    const endDate = new Date(endTime);

    const calendarEl = document.getElementById('bookingCalendar');

    if (calendar) {
        calendar.destroy();
    }

    calendar = new FullCalendar.Calendar(calendarEl, {
        schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
        initialView: 'resourceTimelineDay',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'resourceTimelineDay'
        },
        resourceAreaHeaderContent: 'Venues',
        resources: {
            url: '{{ route("events.calendar") }}',
            method: 'GET',
            extraParams: {
                isResources: true
            }
        },
        datesAboveResources: true,
        slotDuration: '01:00:00', // 1 hour slots
        slotMinTime: startDate.getHours() + ':00:00',
        slotMaxTime: endDate.getHours() + ':00:00',
        validRange: {
            start: startDate,
            end: endDate
        },
        allDaySlot: false,
        selectable: true,
        selectOverlap: false,
        selectAllow: function(selectInfo) {
            // Only allow selection of the exact time slot
            return selectInfo.start.getTime() === startDate.getTime() &&
                   selectInfo.end.getTime() === endDate.getTime();
        },
        events: {
            url: '{{ route("events.calendar") }}',
            method: 'GET',
            extraParams: {
                start: startTime,
                end: endTime
            }
        },
        select: function(info) {
            const venueId = info.resource.id;

            // Check availability for the exact time slot
            $.ajax({
                url: '{{ route("bookings.checkAvailability") }}',
                method: 'POST',
                data: {
                    venue_id: venueId,
                    start_time: startTime,
                    end_time: endTime,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.available) {
                        if (selectedEvent) {
                            calendar.getEventById(selectedEvent.id).remove();
                        }

                        selectedEvent = {
                            id: 'temp_' + Date.now(),
                            resourceId: venueId,
                            title: $('#event_name').val(),
                            start: startTime,
                            end: endTime,
                            backgroundColor: '#28a745',
                            borderColor: '#28a745'
                        };

                        calendar.addEvent(selectedEvent);

                        // Check the corresponding venue checkbox
                        $(`#venue_${venueId}`).prop('checked', true);

                        // Enable submit button
                        $('#submitBookingBtn').prop('disabled', false);
                    } else {
                        alert('This venue is already booked for the selected time slot.');
                    }
                }
            });
        },
        eventDidMount: function(info) {
            // Disable interaction with existing events
            if (!info.event.id.startsWith('temp_')) {
                info.el.style.pointerEvents = 'none';
            }
        },

        eventClick: function(info) {
            if (info.event.id.startsWith('temp_')) {
                info.event.remove();
                selectedEvent = null;

                // Uncheck the corresponding venue checkbox
                const resourceId = info.event.resourceIds[0];
                $(`#venue_${resourceId}`).prop('checked', false);

                // Disable submit button if no venues selected
                if ($('input[name="inhouse_venues[]"]:checked').length === 0 &&
                    $('input[name^="external_venues["][name$="[name]"]').filter(function() {
                        return $(this).val() !== '';
                    }).length === 0) {
                    $('#submitBookingBtn').prop('disabled', true);
                }
            } else {
                alert('This time slot is already booked. Please choose another time or venue.');
            }
        },
        eventDrop: function(info) {
            const venueId = info.event.resourceIds[0];
            const start = info.event.start;
            const end = info.event.end;

            $.ajax({
                url: '{{ route("bookings.checkAvailability") }}',
                method: 'POST',
                data: {
                    venue_id: venueId,
                    start_time: start.toISOString(),
                    end_time: end.toISOString(),
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (!response.available) {
                        alert('The selected time slot is not available. Please choose another time or venue.');
                        info.revert();
                    }
                }
            });
        },
        eventResize: function(info) {
            const venueId = info.event.resourceIds[0];
            const start = info.event.start;
            const end = info.event.end;

            $.ajax({
                url: '{{ route("bookings.checkAvailability") }}',
                method: 'POST',
                data: {
                    venue_id: venueId,
                    start_time: start.toISOString(),
                    end_time: end.toISOString(),
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (!response.available) {
                        alert('The selected time slot is not available. Please choose another time or venue.');
                        info.revert();
                    }
                }
            });
        }
    });

    calendar.render();
}

        // Save draft button
        $('#saveDraftBtn').click(function() {
            $('#bookingForm').append('<input type="hidden" name="status" value="booking_progress">');
            $('#bookingForm').submit();
        });

        // Submit booking button
        $('#submitBookingBtn').click(function(e) {
            e.preventDefault();

            // Prepare confirmation content
            const confirmationContent = `
                <h6>Event Details</h6>
                <table class="table table-bordered">
                    <tr>
                        <th>Event Name</th>
                        <td>${$('#event_name').val()}</td>
                    </tr>
                    <tr>
                        <th>Event Type</th>
                        <td>${$('#event_type option:selected').text()}</td>
                    </tr>
                    <tr>
                        <th>Hosted By</th>
                        <td>${$('#hosted_by option:selected').text()}</td>
                    </tr>
                    ${$('#external_organization').val() ? `
                    <tr>
                        <th>External Organization</th>
                        <td>${$('#external_organization').val()}</td>
                    </tr>
                    ` : ''}
                    <tr>
                        <th>Date/Time</th>
                        <td>${$('#start_time').val()} to ${$('#end_time').val()}</td>
                    </tr>
                </table>

                <h6 class="mt-4">Selected Venues</h6>
                <ul>
                    ${$('input[name="inhouse_venues[]"]:checked').map(function() {
                        return '<li>' + $(this).next('label').text() + '</li>';
                    }).get().join('')}
                    ${$('input[name^="external_venues["][name$="[name]"]').filter(function() {
                        return $(this).val() !== '';
                    }).map(function() {
                        return '<li>' + $(this).val() + ' (External)</li>';
                    }).get().join('')}
                </ul>

                <div class="alert alert-warning mt-3">
                    <strong>Note:</strong> After confirmation, this booking will be finalized and cannot be edited without approval.
                </div>
            `;

            $('#confirmationContent').html(confirmationContent);
        });

        // Confirm submit button in modal
        $('#confirmSubmitBtn').click(function() {
            $('#bookingForm').append('<input type="hidden" name="status" value="confirmed">');
            $('#bookingForm').submit();
            $('#confirmationModal').modal('hide');
        });

        // Poll for booking updates
        setInterval(function() {
            if (calendar) {
                calendar.refetchEvents();
            }
        }, 30000); // Every 30 seconds
    });
</script>
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
<div class="container">
  <h1>Create New Booking</h1>

  <form id="bookingForm" action="{{ route('bookings.store') }}" method="POST">
    @csrf
    <div class="card mb-4">
      <div class="card-header">
        <h5>Date & Time</h5>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-6">
            <div class="mb-3">
              <label for="start_time" class="form-label">From Date/Time</label>
              <input type="datetime-local" class="form-control datepicker" id="start_time" name="start_time" required>
            </div>
          </div>
          <div class="col-md-6">
            <div class="mb-3">
              <label for="end_time" class="form-label">To Date/Time</label>
              <input type="datetime-local" class="form-control datepicker" id="end_time" name="end_time" required>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="card mb-4">
      <div class="card-header">
        <h5>Event Details</h5>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-6">
            <div class="mb-3">
              <label for="event_name" class="form-label">Event/Programme Name</label>
              <input type="text" class="form-control" id="event_name" name="event_name" required>
            </div>

            <div class="mb-3">
              <label for="event_type" class="form-label">Event Type</label>
              <select class="form-select" id="event_type" name="event_type" required>
                <option value="">Select Event Type</option>
                @foreach($eventTypes as $type)
                <option value="{{ $type }}">{{ ucfirst($type) }}</option>
                @endforeach
              </select>
            </div>

            <div class="mb-3">
              <label for="event_mode" class="form-label">Event Mode</label>
              <select class="form-select" id="event_mode" name="event_mode" required>
                <option value="">Select Event Mode</option>
                @foreach($eventModes as $mode)
                <option value="{{ $mode }}">{{ ucfirst($mode) }}</option>
                @endforeach
              </select>
            </div>
          </div>

          <div class="col-md-6">
            <div class="mb-3">
              <label for="hosted_by" class="form-label">Hosted By</label>
              <select class="form-select" id="hosted_by" name="hosted_by" required>
                <option value="">Select Host</option>
                @foreach($users as $user)
                <option value="{{ $user->id }}">{{ $user->name }}</option>
                @endforeach
              </select>
            </div>

            <div class="mb-3" id="externalOrgField" style="display: none;">
              <label for="external_organization" class="form-label">External Department/Organization</label>
              <input type="text" class="form-control" id="external_organization" name="external_organization">
            </div>

            <div class="mb-3">
              <label for="coordinator_id" class="form-label">Programme Coordinator</label>
              <select class="form-select" id="coordinator_id" name="coordinator_id" required>
                <option value="">Select Coordinator</option>
                @foreach($users as $user)
                <option value="{{ $user->id }}">{{ $user->name }}</option>
                @endforeach
              </select>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-6">
            <div class="mb-3">
              <label for="participants_count" class="form-label">Number of Participants Expected</label>
              <input type="number" class="form-control" id="participants_count" name="participants_count" min="1"
                required>
            </div>
          </div>

          <div class="col-md-6">
            <div class="mb-3">
              <label for="seat_layout" class="form-label">Required Seat Layout</label>
              <select class="form-select" id="seat_layout" name="seat_layout" required>
                <option value="">Select Seat Layout</option>
                @foreach($seatLayouts as $layout)
                <option value="{{ $layout }}">{{ str_replace('_', ' ', ucfirst($layout)) }}</option>
                @endforeach
                <option value="custom">Custom Layout</option>
              </select>
            </div>
            <div class="mb-3" id="customSeatLayoutField" style="display: none;">
              <label for="custom_seat_layout" class="form-label">Custom Seat Layout Details</label>
              <input type="text" class="form-control" id="custom_seat_layout" name="custom_seat_layout">
            </div>
          </div>
        </div>

        <div class="mb-3">
          <label for="additional_requirements" class="form-label">Additional Requirements</label>
          <textarea class="form-control" id="additional_requirements" name="additional_requirements"
            rows="3"></textarea>
        </div>
      </div>
    </div>



    <div class="card mb-4">
      <div class="card-header">
        <h5>Venue Selection</h5>
      </div>
      <div class="card-body">
        <div class="mb-3">
          <label class="form-label">Venue Type</label>
          <div class="form-check">
            <input class="form-check-input" type="radio" name="venue_type" id="venue_type_inhouse" value="inhouse"
              checked>
            <label class="form-check-label" for="venue_type_inhouse">
              Inhouse Venues
            </label>
          </div>
          <div class="form-check">
            <input class="form-check-input" type="radio" name="venue_type" id="venue_type_external" value="external">
            <label class="form-check-label" for="venue_type_external">
              External Venues
            </label>
          </div>
          <div class="form-check">
            <input class="form-check-input" type="radio" name="venue_type" id="venue_type_both" value="both">
            <label class="form-check-label" for="venue_type_both">
              Both Inhouse and External
            </label>
          </div>
        </div>

        <div id="inhouseVenuesSection">
          <h6>Inhouse Venues</h6>
          <div class="mb-3">
            @foreach($venues as $venue)
            <div class="form-check">
              <input class="form-check-input venue-checkbox" type="checkbox" name="inhouse_venues[]"
                id="venue_{{ $venue->id }}" value="{{ $venue->id }}">
              <label class="form-check-label" for="venue_{{ $venue->id }}">
                {{ $venue->name }} (Capacity: {{ $venue->seating_capacity }})
              </label>
            </div>
            @endforeach
          </div>
        </div>

        <div id="externalVenuesSection" style="display: none;">
          <h6>External Venues</h6>
          <div id="externalVenuesContainer">
            <div class="external-venue mb-3">
              <div class="row">
                <div class="col-md-4">
                  <input type="text" class="form-control" name="external_venues[0][name]" placeholder="Venue Name">
                </div>
                <div class="col-md-4">
                  <input type="number" class="form-control" name="external_venues[0][capacity]" placeholder="Capacity">
                </div>
                <div class="col-md-4">
                  <input type="text" class="form-control" name="external_venues[0][details]" placeholder="Details">
                </div>
              </div>
            </div>
          </div>
          <button type="button" class="btn btn-sm btn-secondary" id="addExternalVenue">Add Another Venue</button>
        </div>

        <div class="mb-3">
          <label class="form-label">Required Amenities</label>
          <div class="row">
            <div class="col-md-4">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="amenities[]" id="amenity_internet"
                  value="internet">
                <label class="form-check-label" for="amenity_internet">Internet</label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="amenities[]" id="amenity_projector"
                  value="projector">
                <label class="form-check-label" for="amenity_projector">Projector</label>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="amenities[]" id="amenity_pa_system"
                  value="pa_system">
                <label class="form-check-label" for="amenity_pa_system">PA System</label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="amenities[]" id="amenity_ac" value="ac">
                <label class="form-check-label" for="amenity_ac">Air Conditioning</label>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="amenities[]" id="amenity_catering"
                  value="catering">
                <label class="form-check-label" for="amenity_catering">Catering</label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="amenities[]" id="amenity_other" value="other">
                <label class="form-check-label" for="amenity_other">Other</label>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="card mb-4" id="calendarSection" style="display: none;">
      <div class="card-header">
        <h5>Venue Availability Calendar</h5>
      </div>
      <div class="card-body">
        <div id="bookingCalendar"></div>
        <div class="alert alert-info mt-3">
          <strong>Instructions:</strong> Select a venue by clicking on an available time slot. Drag and drop to adjust
          timing or move to another venue.
        </div>
      </div>
    </div>

    <div class="d-flex justify-content-between">
      <button type="button" class="btn btn-secondary" id="saveDraftBtn">Save Draft</button>
      <button type="button" class="btn btn-primary" id="submitBookingBtn" disabled data-bs-toggle="modal"
        data-bs-target="#confirmationModal">Submit Booking</button>
    </div>
  </form>
</div>

<!-- Confirmation Modal -->
<div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel"
  aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="confirmationModalLabel">Booking Confirmation</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="confirmationContent">
        <!-- Content will be loaded via JavaScript -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" id="confirmSubmitBtn">Confirm Booking</button>
      </div>
    </div>
  </div>
</div>

@push('styles')
<style>
  .fc-event {
    cursor: pointer;
  }

  .fc-day-disabled {
    background-color: #f8f9fa;
  }
</style>
@endpush

{{-- @push('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales-all.min.js"></script>

@endpush --}}
@endsection