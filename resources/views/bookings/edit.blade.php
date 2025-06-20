@extends('layouts/layoutMaster')


@section('title', 'Edit Booking')

@section('content')
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
        }).trigger('change');

        // Show/hide custom seat layout field
        $('#seat_layout').change(function() {
            if ($(this).val() === 'custom') {
                $('#customSeatLayoutField').show();
                $('#custom_seat_layout').prop('required', true);
            } else {
                $('#customSeatLayoutField').hide();
                $('#custom_seat_layout').prop('required', false);
            }
        }).trigger('change');

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
        let externalVenueCounter = {{ $booking->venues->where('pivot.is_external', true)->count() }};
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
                    right: 'resourceTimelineDay,resourceTimelineWeek'
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
                slotMinTime: '08:00:00',
                slotMaxTime: '20:00:00',
                validRange: {
                    start: startDate,
                    end: endDate
                },
                events: {
                    url: '{{ route("events.calendar") }}',
                    method: 'GET',
                    extraParams: {
                        start: startTime,
                        end: endTime,
                        ignore_id: '{{ $booking->id }}'
                    }
                },
                editable: true,
                selectable: true,
                selectMirror: true,
                dayMaxEvents: true,
                select: function(info) {
                    const venueId = info.resource.id;
                    const start = info.start;
                    const end = info.end;

                    $.ajax({
                        url: '{{ route("bookings.checkAvailability") }}',
                        method: 'POST',
                        data: {
                            venue_id: venueId,
                            start_time: start.toISOString(),
                            end_time: end.toISOString(),
                            ignore_id: '{{ $booking->id }}',
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
                                    start: start,
                                    end: end,
                                    backgroundColor: '#28a745',
                                    borderColor: '#28a745'
                                };

                                calendar.addEvent(selectedEvent);

                                // Check the corresponding venue checkbox
                                $(`#venue_${venueId}`).prop('checked', true);
                            } else {
                                alert('The selected time slot is not available. Please choose another time or venue.');
                            }
                        }
                    });
                },
                eventClick: function(info) {
                    if (info.event.id.startsWith('temp_')) {
                        info.event.remove();
                        selectedEvent = null;

                        // Uncheck the corresponding venue checkbox
                        const resourceId = info.event.resourceIds[0];
                        $(`#venue_${resourceId}`).prop('checked', false);
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
                            ignore_id: '{{ $booking->id }}',
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
                            ignore_id: '{{ $booking->id }}',
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

            // Add existing selected venues to calendar
            @foreach($booking->venues->where('pivot.is_external', false) as $venue)
                const event_{{ $venue->id }}  = {
                    id: 'temp_{{ $venue->id }}',
                    resourceId: '{{ $venue->id }}',
                    title: '{{ $booking->event_name }}',
                    start: '{{ $booking->start_time->toIso8601String() }}',
                    end: '{{ $booking->end_time->toIso8601String() }}',
                    backgroundColor: '#28a745',
                    borderColor: '#28a745'
                };
                calendar.addEvent(event);
            @endforeach
        }

        // Initialize calendar if all required fields are filled
        checkRequiredFields();

        // Save draft button
        $('#saveDraftBtn').click(function() {
            $('#bookingForm').append('<input type="hidden" name="status" value="booking_progress">');
            $('#bookingForm').submit();
        });
    });
</script>
@endsection

@section('content')
<div class="container">
  <h1>Edit Booking</h1>

  <form id="bookingForm" action="{{ route('bookings.update', $booking) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="card mb-4">
      <div class="card-header">
        <h5>Event Details</h5>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-6">
            <div class="mb-3">
              <label for="event_name" class="form-label">Event/Programme Name</label>
              <input type="text" class="form-control" id="event_name" name="event_name"
                value="{{ old('event_name', $booking->event_name) }}" required>
            </div>

            <div class="mb-3">
              <label for="event_type" class="form-label">Event Type</label>
              <select class="form-select" id="event_type" name="event_type" required>
                <option value="">Select Event Type</option>
                @foreach($eventTypes as $type)
                <option value="{{ $type }}" {{ old('event_type', $booking->event_type) == $type ? 'selected' : '' }}>
                  {{ ucfirst($type) }}
                </option>
                @endforeach
              </select>
            </div>

            <div class="mb-3">
              <label for="event_mode" class="form-label">Event Mode</label>
              <select class="form-select" id="event_mode" name="event_mode" required>
                <option value="">Select Event Mode</option>
                @foreach($eventModes as $mode)
                <option value="{{ $mode }}" {{ old('event_mode', $booking->event_mode) == $mode ? 'selected' : '' }}>
                  {{ ucfirst($mode) }}
                </option>
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
                <option value="{{ $user->id }}" {{ old('hosted_by', $booking->hosted_by) == $user->id ? 'selected' : ''
                  }}>
                  {{ $user->name }}
                </option>
                @endforeach
              </select>
            </div>

            <div class="mb-3" id="externalOrgField"
              style="{{ old('hosted_by', $booking->hosted_by) && !str_contains(App\Models\User::find(old('hosted_by', $booking->hosted_by))->name, 'External') ? 'display: none;' : '' }}">
              <label for="external_organization" class="form-label">External Department/Organization</label>
              <input type="text" class="form-control" id="external_organization" name="external_organization"
                value="{{ old('external_organization', $booking->external_organization) }}">
            </div>

            <div class="mb-3">
              <label for="coordinator_id" class="form-label">Programme Coordinator</label>
              <select class="form-select" id="coordinator_id" name="coordinator_id" required>
                <option value="">Select Coordinator</option>
                @foreach($users as $user)
                <option value="{{ $user->id }}" {{ old('coordinator_id', $booking->coordinator_id) == $user->id ?
                  'selected' : '' }}>
                  {{ $user->name }}
                </option>
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
                value="{{ old('participants_count', $booking->participants_count) }}" required>
            </div>
          </div>

          <div class="col-md-6">
            <div class="mb-3">
              <label for="seat_layout" class="form-label">Required Seat Layout</label>
              <select class="form-select" id="seat_layout" name="seat_layout" required>
                <option value="">Select Seat Layout</option>
                @foreach($seatLayouts as $layout)
                <option value="{{ $layout }}" {{ old('seat_layout', $booking->seat_layout) == $layout ? 'selected' : ''
                  }}>
                  {{ str_replace('_', ' ', ucfirst($layout)) }}
                </option>
                @endforeach
                <option value="custom" {{ old('seat_layout', $booking->seat_layout) == 'custom' ? 'selected' : ''
                  }}>Custom Layout</option>
              </select>
            </div>
            <div class="mb-3" id="customSeatLayoutField"
              style="{{ old('seat_layout', $booking->seat_layout) == 'custom' ? '' : 'display: none;' }}">
              <label for="custom_seat_layout" class="form-label">Custom Seat Layout Details</label>
              <input type="text" class="form-control" id="custom_seat_layout" name="custom_seat_layout"
                value="{{ old('custom_seat_layout', $booking->custom_seat_layout) }}">
            </div>
          </div>
        </div>

        <div class="mb-3">
          <label for="additional_requirements" class="form-label">Additional Requirements</label>
          <textarea class="form-control" id="additional_requirements" name="additional_requirements"
            rows="3">{{ old('additional_requirements', $booking->additional_requirements) }}</textarea>
        </div>
      </div>
    </div>

    <div class="card mb-4">
      <div class="card-header">
        <h5>Date & Time</h5>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-6">
            <div class="mb-3">
              <label for="start_time" class="form-label">From Date/Time</label>
              <input type="datetime-local" class="form-control" id="start_time" name="start_time"
                value="{{ old('start_time', $booking->start_time->format('Y-m-d\TH:i')) }}" required>
            </div>
          </div>
          <div class="col-md-6">
            <div class="mb-3">
              <label for="end_time" class="form-label">To Date/Time</label>
              <input type="datetime-local" class="form-control" id="end_time" name="end_time"
                value="{{ old('end_time', $booking->end_time->format('Y-m-d\TH:i')) }}" required>
            </div>
          </div>
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
            <input class="form-check-input" type="radio" name="venue_type" id="venue_type_inhouse" value="inhouse" {{
              $booking->venues->where('pivot.is_external', false)->count() > 0 ? 'checked' : '' }}>
            <label class="form-check-label" for="venue_type_inhouse">
              Inhouse Venues
            </label>
          </div>
          <div class="form-check">
            <input class="form-check-input" type="radio" name="venue_type" id="venue_type_external" value="external" {{
              $booking->venues->where('pivot.is_external', true)->count() > 0 ? 'checked' : '' }}>
            <label class="form-check-label" for="venue_type_external">
              External Venues
            </label>
          </div>
          <div class="form-check">
            <input class="form-check-input" type="radio" name="venue_type" id="venue_type_both" value="both" {{
              $booking->venues->where('pivot.is_external', false)->count() > 0 &&
            $booking->venues->where('pivot.is_external', true)->count() > 0 ? 'checked' : '' }}>
            <label class="form-check-label" for="venue_type_both">
              Both Inhouse and External
            </label>
          </div>
        </div>

        <div id="inhouseVenuesSection" style="{{ old('venue_type',
                    $booking->venues->where('pivot.is_external', false)->count() > 0 ? '' : 'display: none;') }}">
          <h6>Inhouse Venues</h6>
          <div class="mb-3">
            @foreach($venues as $venue)
            <div class="form-check">
              <input class="form-check-input venue-checkbox" type="checkbox" name="inhouse_venues[]"
                id="venue_{{ $venue->id }}" value="{{ $venue->id }}" {{ in_array($venue->id,
              $booking->venues->where('pivot.is_external', false)->pluck('id')->toArray()) ? 'checked' : '' }}>
              <label class="form-check-label" for="venue_{{ $venue->id }}">
                {{ $venue->name }} (Capacity: {{ $venue->seating_capacity }})
              </label>
            </div>
            @endforeach
          </div>
        </div>

        <div id="externalVenuesSection" style="{{ old('venue_type',
                    $booking->venues->where('pivot.is_external', true)->count() > 0 ? '' : 'display: none;') }}">
          <h6>External Venues</h6>
          <div id="externalVenuesContainer">
            @foreach($booking->venues->where('pivot.is_external', true) as $index => $venue)
            <div class="external-venue mb-3">
              <div class="row">
                <div class="col-md-4">
                  <input type="text" class="form-control" name="external_venues[{{ $index }}][name]"
                    placeholder="Venue Name" value="{{ $venue->name }}">
                </div>
                <div class="col-md-4">
                  <input type="number" class="form-control" name="external_venues[{{ $index }}][capacity]"
                    placeholder="Capacity" value="{{ $venue->seating_capacity }}">
                </div>
                <div class="col-md-4">
                  <input type="text" class="form-control" name="external_venues[{{ $index }}][details]"
                    placeholder="Details" value="{{ $venue->pivot->external_venue_details }}">
                </div>
              </div>
              @if($index > 0)
              <button type="button" class="btn btn-sm btn-danger remove-external-venue mt-2">Remove</button>
              @endif
            </div>
            @endforeach
            @if($booking->venues->where('pivot.is_external', true)->count() === 0)
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
            @endif
          </div>
          <button type="button" class="btn btn-sm btn-secondary" id="addExternalVenue">Add Another Venue</button>
        </div>

        <div class="mb-3">
          <label class="form-label">Required Amenities</label>
          <div class="row">
            <div class="col-md-4">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="amenities[]" id="amenity_internet"
                  value="internet" {{ in_array('internet', old('amenities', $booking->amenities ?? [])) ? 'checked' : ''
                }}>
                <label class="form-check-label" for="amenity_internet">Internet</label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="amenities[]" id="amenity_projector"
                  value="projector" {{ in_array('projector', old('amenities', $booking->amenities ?? [])) ? 'checked' :
                '' }}>
                <label class="form-check-label" for="amenity_projector">Projector</label>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="amenities[]" id="amenity_pa_system"
                  value="pa_system" {{ in_array('pa_system', old('amenities', $booking->amenities ?? [])) ? 'checked' :
                '' }}>
                <label class="form-check-label" for="amenity_pa_system">PA System</label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="amenities[]" id="amenity_ac" value="ac" {{
                  in_array('ac', old('amenities', $booking->amenities ?? [])) ? 'checked' : '' }}>
                <label class="form-check-label" for="amenity_ac">Air Conditioning</label>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="amenities[]" id="amenity_catering"
                  value="catering" {{ in_array('catering', old('amenities', $booking->amenities ?? [])) ? 'checked' : ''
                }}>
                <label class="form-check-label" for="amenity_catering">Catering</label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="amenities[]" id="amenity_other" value="other" {{
                  in_array('other', old('amenities', $booking->amenities ?? [])) ? 'checked' : '' }}>
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
      <button type="submit" class="btn btn-primary">Update Booking</button>
    </div>
  </form>
</div>


@endsection