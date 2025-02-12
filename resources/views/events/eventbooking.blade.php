@extends('layouts/layoutMaster')

@section('title', 'Events - ')

@section('vendor-style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/formvalidation/dist/css/formValidation.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/animate-css/animate.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/jquery-timepicker/jquery-timepicker.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/flatpickr/flatpickr.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.css') }}" />
    <link rel="stylesheet"
        href="{{ asset('assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/jquery-timepicker/jquery-timepicker.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/pickr/pickr-themes.css') }}" />



@endsection

@section('vendor-script')
    <script src="{{ asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>

    <script src="{{ asset('assets/vendor/libs/formvalidation/dist/js/FormValidation.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/moment/moment.js') }}"></script>

    <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/jquery-sticky/jquery-sticky.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/cleavejs/cleave.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/cleavejs/cleave-phone.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/moment/moment.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/jquery-timepicker/jquery-timepicker.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/pickr/pickr.js') }}"></script>



@endsection

@section('page-script')
    <script src="{{ asset('assets/js/form-layouts.js') }}"></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar-scheduler@6.1.15/index.global.min.js'></script>
    {{--  <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js'></script>  --}}
    {{--  <script src="{{ asset('assets/vendor/libs/fullcalendar/fullcalendar.js') }}"></script>  --}}
    {{--  <script src="{{ url('assets/js/plugins/fullcalendar.min.js') }}"></script>  --}}

    <script>
        // * Pickers with jQuery dependency (jquery)
        $(function() {
          $('.Availability').hide();
          $('.additional_details').hide();

          {{--  $('.event-form').hide();
         var designationSubmit = document.querySelector('.add-new');
          designationSubmit.onclick = function () {
            alert();
          }
          $('#newBooking').click(function(){
            alert();
            $('.event-form').show();
            $('.event-list').hide();
          })  --}}

            const
                flatpickrDateTime_from = document.querySelector('#flatpickr-datetime-from'),
                flatpickrDateTime_to = document.querySelector('#flatpickr-datetime-to');

            flatpickrDateTime_from.flatpickr({
                enableTime: true,
                dateFormat: 'Y-m-d H:i'
            });
            flatpickrDateTime_to.flatpickr({
                enableTime: true,
                dateFormat: 'Y-m-d H:i'
            });

            function initializeBooking() {


              $('.additional_details').show();


              const event_id = $('#event_id').val();
              const event_name = $('#event_name').val();
              const host = $('#host').val();
              const venue_type = $('#venue_type').val();
              const no_of_participants = $('#no_of_participants').val();
              var from =$('#flatpickr-datetime-from').val();
              var to = $('#flatpickr-datetime-to').val();

              if($('#event_id').val() == 0){

                $.ajax({
                  data:  {
                    from_date:from,
                    to_date:to,
                    type:'initial',
                    "_token": "{{ csrf_token() }}",
                },
                  url: `${baseUrl}event/store`,
                  type: 'POST',

                  success: function (data) {
                    console.log(data)

                    $('#event_id').val(data.event_id)


                  },
                  error: function (err) {

                    Swal.fire({
                      title: 'Oh Sorry!',
                      text: `${status}`,
                      icon: 'error',
                      customClass: {
                        confirmButton: 'btn btn-success'
                      }
                    });
                  }
                });
              }
              else{


                $.ajax({
                  data:  {
                    from_date:from,
                    to_date:to,
                    type:'update',
                    event_id:event_id,
                    event_name:event_name,
                    host:host,
                    venue_type:venue_type,
                    no_of_participants:no_of_participants,

                    "_token": "{{ csrf_token() }}",
                },

                  url: `${baseUrl}event/update/${event_id}`,
                  type: 'POST',

                  success: function (data) {

                    $('#event_id').val(data.event_id);
                    {{--  $('#event_name').val(data.event_name);  --}}

                  },
                  error: function (err) {
                    $('#DesignationModal').modal('hide');
                    Swal.fire({
                      title: 'Oh Sorry!',
                      text: `error`,
                      icon: 'error',
                      customClass: {
                        confirmButton: 'btn btn-success'
                      }
                    });
                  }
                });
              }


              loadBookings();
          }

            function loadBookings() {



                  $('.Availability').show();


                  let dateStr = $('#flatpickr-datetime-from').val();
                  let dateStr_to = $('#flatpickr-datetime-to').val();

                  let start_slot_time = dateStr.split(' ')[1]; // Extracts the time part (12:00)
                  let end_slot_time = dateStr_to.split(' ')[1]; // Extracts the time part (12:00)

                  let start_slot_date = dateStr.split(' ')[0]; // Extracts the time part (12:00)
                  let end_slot_date = dateStr_to.split(' ')[0]; // Extracts the time part (12:00)

                  let formattedStartDate = start_slot_date+' 00:00:00';
                  let formattedToDate = end_slot_date+' 23:59:59';

                  let date1 = new Date(formattedStartDate.replace(" ", "T")); // Replace space with 'T' for ISO format
                  let date2 = new Date(formattedToDate.replace(" ", "T"));






                  let clickTimer = null;
                    var calendarEl = document.getElementById('hallAvailabilityCalendar');
                    var calendar = new FullCalendar.Calendar(calendarEl, {
                      schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
                        eventDurationEditable: false,
                        editable: true,
                        {{--  draggable: true,  --}}
                        navLinks: true,
                        {{--  eventLimit: true,  --}}
                        selectable: true,
                        {{--  selectHelper: true,  --}}
                        eventOverlap: false,
                        selectOverlap: true,
                        slotEventOverlap: true,
                        refetchResourcesOnNavigate: true,
                        initialView: 'resourceTimelineWeek',
                        {{--  initialView: 'customWeek',  --}}
                        slotMinTime: start_slot_time,
                        slotMaxTime: end_slot_time,
                        selectMirror: true,
                        selectable: true,
                        height: "auto",
                        {{--  defaultView: 'customWeek',
                          views: {
                            customWeek: {
                                type: 'resourceTimeline',
                                duration: {
                                    days: 5
                                },
                                slotDuration: {
                                    days: 1
                                },
                                buttonText: 'Week'
                            }
                        },  --}}
                        validRange: {
                            start: date1,
                            end: date2
                        },
                        headerToolbar: {
                            start: 'promptResource,prev,next',
                            //   left: 'prev,next',
                            center: 'title',
                            end: 'customWeek,listWeek'
                        },
                        customButtons: {
                            promptResource: {
                                text: '+ External Venue',
                                click: function() {
                                    Swal.fire({
                                        title: 'External Venue Details',
                                        html: '<div class="form-group">' +
                                            '<input id="input-field1" required="" type="text" placeholder="Venue Name *" class="form-control" /><br>' +
                                            '<input id="input-field2" required="" placeholder="Seating Capacity *" type="text" class="form-control" />' +
                                            '</div>',
                                        showCancelButton: true,
                                        confirmButtonClass: 'btn btn-success',
                                        cancelButtonClass: 'btn btn-danger',
                                        buttonsStyling: false
                                    }).then(function(result) {
                                        var title = $('#input-field1').val();
                                        var capacity = $('#input-field2').val();
                                        if (title && capacity) {
                                            $('#hallAvailabilityCalendar').fullCalendar(
                                                'addResource', {
                                                    location: 'External',
                                                    title: title,
                                                    id: title + $('#schedule_id').val(),
                                                    type: 'External',
                                                    resourceId: title,
                                                    SeatingCapacity: capacity


                                                },
                                                true // scroll to the new resource?

                                            );

                                            Swal.fire({
                                                type: 'success',
                                                html: ' <strong>' +
                                                    $('#input-field1').val() +
                                                    '</strong> Added as External Venue.',
                                                confirmButtonClass: 'btn btn-success',
                                                buttonsStyling: false

                                            })
                                        }

                                    }).catch(swal.noop)

                                    // $('#hallAvailabilityCalendar').fullCalendar('refetchResources');
                                }
                            }
                        },


                        resourceAreaColumns: [

                            {
                                headerContent: 'Venue',
                                field: 'title',
                                width:'30%',
                                {{--  cellClassNames:'bg-label-dark'  --}}
                            },
                            {
                                headerContent: 'Capacity',
                                field: 'SeatingCapacity',
                                 width: '10%',
                                 cellClassNames:'bg-label-primary'
                            }
                        ],
                        resources: '/getVenues/?event_id=' + $('#event_id').val(),

                        {{--  resourceRender: function(resourceObj, labelTds, bodyTds) {
                          labelTds.on('click', function() {
                              // alert($(this).closest('tr').data('resource-id'));
                              venueDetails($(this).closest('tr').data('resource-id'));
                          });
                        },  --}}
                        events: '/events/loadBookingsAllVenue/'+$("#event_id").val(),
                        displayEventTime:false,
                        //full calendar poll events
                        loading: function(isLoading, view) {
                            if (isLoading) { // isLoading gives boolean value
                                //show your loader here
                                console.log("loading events");

                            } else {
                              console.log("loading events completed");
                                //hide your loader here

                            }
                        },

                        eventDidMount: function(info) {
                          // Add a Bootstrap popover
                          console.log(info.event);
                          const popover = new bootstrap.Popover(info.el, {
                            title: info.event.title,
                            content: "Duration :"+info.event.start+' - '+info.event.end+
                            "Faculty :"+info.event.extendedProps.courseDirector+
                                      " ,Coordinator :"+info.event.extendedProps.coursecordinator,
                                       {{--  info.event.extendedProps.description || 'No description available',  --}}
                            trigger: 'hover',
                            placement: 'top',
                            delay: 200
                          });
                          if (!info.event.extendedProps.isEditable) {
                            info.event.setProp('editable', false); // Disable drag-and-drop
                          }

                          // Optionally add custom styles or attributes
                          info.el.style.cursor = 'pointer';
                          info.el.style.cursor = info.event.extendedProps.isEditable ? 'pointer' : 'not-allowed';
                          {{--  if (info.el) {
                            info.el.style.height = '100px'; // Set event height
                        }  --}}
                        info.el.style.marginTop = '30px';
                        info.el.style.marginBottom = '30px';

                        },
                        eventAllow: function(dropInfo, draggedEvent) {
                          // Prevent dragging if the event is not draggable
                          return draggedEvent.extendedProps.isDraggable || false;
                      },
                        eventClick: function(info) {
                          if (clickTimer) {
                            clearTimeout(clickTimer); // Cancel the single-click timer
                            clickTimer = null;

                            // Handle double-click action
                            {{--  alert('Double-click detected on event: ' + info.event.title);  --}}
                            if (info.event.extendedProps.editable == true) {
                              swal({
                                  text: "Are you sure, <br />Do you want to remove it?",
                                  type: 'warning',
                                  showCancelButton: true,
                                  confirmButtonText: 'Yes, Remove!',
                                  cancelButtonText: 'No, Continue',
                                  confirmButtonClass: "btn btn-success",
                                  cancelButtonClass: "btn btn-warning",
                                  buttonsStyling: false
                              }).then(function() {
                                  var id = info.event.extendedProps.id;
                                  $.ajax({
                                      url: "/training/programme/deleteVenueBooking",
                                      type: "GET",
                                      data: {
                                          id: id
                                      },
                                      success: function() {
                                          demo.showNotification('top', 'right',
                                              'success', 'check_circle',
                                              'Venue Booking Removed');
                                          calendar.fullCalendar('refetchEvents');
                                          // alert("Event Removed");
                                          $('.popover').remove();

                                      }
                                  })
                              })


                          }

                          } else {
                            clickTimer = setTimeout(() => {
                              // Handle single-click action (if needed)
                              {{--  alert('Single-click detected on event: ' + info.event.title);  --}}
                              clickTimer = null;
                            }, 300); // Timeout to differentiate single vs. double click
                          }
                        },


                      eventReceive: function(event) { // called when a proper external event is dropped
                        console.log('eventReceive', event);
                    },
                    select: function(selectionInfo) {
                      const resourceId = selectionInfo.resource?.id; // Resource ID
                      const resourceTitle = selectionInfo.resource?.title; // Resource Title

                      console.log('Selected Resource ID:', resourceId);
                      console.log('Selected Resource Title:', resourceTitle);
                      console.log('Start:', selectionInfo.start);
                      console.log('End:', selectionInfo.end);
                      console.log('All Day:', selectionInfo.allDay);
                      const event_id = $('#event_id').val();
                      const event_name = $('#event_name').val();
                      const host = $('#host').val();
                      const venue_type = $('#venue_type').val();
                      const no_of_participants = $('#no_of_participants').val();
                      var from =$('#flatpickr-datetime-from').val();
                      var to = $('#flatpickr-datetime-to').val();


                      if (resourceId) {

                        $.ajax({
                          data:  {
                            from_date:from,
                            to_date:to,
                            type:'update',
                            venue_id:resourceId,
                            "_token": "{{ csrf_token() }}",
                        },

                          url: `${baseUrl}venueAvailability`,
                          type: 'POST',

                          success: function (data) {
                            if(data.status == 1){

                              $.ajax({
                                data:  {
                                  from_date:from,
                                  to_date:to,
                                  type:'update',
                                  venue_id:resourceId,
                                  event_id:event_id,
                                  event_name:event_name,
                                  host:host,
                                  venue_type:venue_type,
                                  no_of_participants:no_of_participants,

                                  "_token": "{{ csrf_token() }}",
                              },

                                url: `${baseUrl}event/update/${event_id}`,
                                type: 'POST',

                                success: function (status) {
                                  // sweetalert
                                  Swal.fire({
                                    icon: 'success',
                                    title: `Success!`,
                                    text: `Venue Booked Successfully.`,
                                    customClass: {
                                      confirmButton: 'btn btn-success'
                                    }
                                  }).then((result) => {
                                calendar.refetchEvents();
                                  });

                                },
                                error: function (err) {
                                  Swal.fire({
                                    title: 'Oh Sorry!',
                                    text: `${status}`,
                                    icon: 'error',
                                    customClass: {
                                      confirmButton: 'btn btn-success'
                                    }
                                  });
                                }
                              });


                            }
                            else{
                              Swal.fire({
                                title: 'Oh Sorry!',
                                text: `Slot are not avilable`,
                                icon: 'error',
                                customClass: {
                                  confirmButton: 'btn btn-success'
                                }
                              });
                            }


                          },
                          error: function (err) {
                            Swal.fire({
                              title: 'Oh Sorry!',
                              text: `${status}`,
                              icon: 'error',
                              customClass: {
                                confirmButton: 'btn btn-success'
                              }
                            });
                          }
                        });





                      }
                  }


                    });
                    calendar.render();
                    const adjustSlotHeight = () => {
                      const timeGridCells = document.querySelectorAll('.fc-timegrid-slot');
                      timeGridCells.forEach(slot => {
                          slot.style.height = '80px'; // Adjust slot height
                      });
                  };

                  adjustSlotHeight();


            }
            $('#flatpickr-datetime-from').change(function(){
              console.log($('#flatpickr-datetime-from').val());
            });

            $(".flatpickr-datetime").change(function() {
              if (($('#flatpickr-datetime-from').val() != '') && ($('#flatpickr-datetime-to').val() != '') && $('#event_name').val() != '') {
              initializeBooking();
              }
            })
            $("#event_name").blur(function() {
              if (($('#flatpickr-datetime-from').val() != '') && ($('#flatpickr-datetime-to').val() != '') && $('#event_name').val() != '') {
              initializeBooking();
              }
            })

        });

        $(function() {


            var dataTablePermissions = $('.datatables-designation'),
                dt_permission,
                permissionList = baseUrl + 'eventbooking/list';


            // ajax setup
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Users List datatable
            if (dataTablePermissions.length) {
                dt_permission = dataTablePermissions.DataTable({
                    ajax: {
                        url: permissionList
                    }, // JSON file to add data
                    //ajax: assetsPath + 'json/permissions-list1.json', // JSON file to add data
                    columns: [
                        // columns according to JSON
                        {
                            data: ''
                        },
                        {
                            data: 'event_name'
                        },
                        {
                            data: 'host'
                        },
                        {
                            data: 'event_type'
                        },
                        {
                            data: 'from_date'
                        },
                        {
                            data: 'to_date'
                        },
                        {
                            data: 'no_of_participants'
                        },
                        {
                            data: 'status'
                        },
                        {
                            data: 'booked_at'
                        },
                        {
                            data: ''
                        }
                    ],
                    columnDefs: [{
                            // For Responsive
                            className: 'control',
                            orderable: false,
                            searchable: false,
                            responsivePriority: 2,
                            targets: 0,
                            render: function(data, type, full, meta) {
                                return '';
                            }
                        },

                        {
                            // Name
                            targets: 2,
                            render: function(data, type, full, meta) {
                                var $type = ((full['host'] == 1) ? 'in house' : (full['type'] ==
                                    2) ? 'External' : 'Other');
                                return '<span class="text-nowrap">' + $type + '</span>';
                            }
                        },
                        {
                            // Name
                            targets: 3,
                            render: function(data, type, full, meta) {
                                var $type = ((full['event_type'] == 1) ? 'Meeting' : (full[
                                    'type'] == 2) ? 'Interview' : 'Exam');
                                return '<span class="text-nowrap">' + $type + '</span>';
                            }
                        },
                        {
                            // Name
                            targets: 8,
                            render: function(data, type, full, meta) {
                                var $name = full['formatted_requested_at'];
                                return '<span class="text-nowrap">' + $name + '</span>';
                            }
                        },
                        {
                            // User Role
                            targets: 9,
                            render: function(data, type, full, meta) {
                                var $status = full['status'];
                                $out = ($status == 1 ?
                                    '<a><span class="badge bg-label-success m-1">Approved</span></a>' :
                                    ($status == 2 ?
                                        '<a><span class="badge bg-label-danger m-1">Rejected</span></a>' :
                                        '<a><span class="badge bg-label-warning m-1">Pending</span></a>'
                                        ))
                                return $out;
                            }
                        },


                        {
                            // Actions
                            targets: -1,
                            searchable: false,
                            title: 'Actions',
                            orderable: false,
                            render: function(data, type, full, meta) {
                                if (full['status'] == 0) {
                                    return (
                                        '<span class="text-nowrap"><button class="btn btn-sm btn-icon me-2 edit-designation" data-id="' +
                                        full['id'] +
                                        '" data-bs-target="#DesignationModal" data-bs-toggle="modal" data-bs-dismiss="modal"><i class="ti ti-edit"></i></button>' +
                                        '<button class="btn btn-sm btn-icon delete-record" data-id="' +
                                        full['id'] +
                                        '"><i class="ti ti-trash"></i></button></span>'
                                    );
                                } else {
                                    return (
                                        '<span class="text-nowrap">' +
                                        '</span>'
                                    );
                                }

                            }
                        }
                    ],
                    {{--  order: [[1, 'asc']],  --}}
                    dom: '<"row mx-1"' +
                        '<"col-sm-12 col-md-3" l>' +
                        '<"col-sm-12 col-md-9"<"dt-action-buttons text-xl-end text-lg-start text-md-end text-start d-flex align-items-center justify-content-md-end justify-content-center flex-wrap me-1"<"me-3"f>B>>' +
                        '>t' +
                        '<"row mx-2"' +
                        '<"col-sm-12 col-md-6"i>' +
                        '<"col-sm-12 col-md-6"p>' +
                        '>',
                    language: {
                        sLengthMenu: 'Show _MENU_',
                        search: 'Search',
                        searchPlaceholder: 'Search..'
                    },
                    // Buttons with Dropdown
                    buttons: [{
                        text: 'New Booking',
                        className: 'add-new new-booking btn btn-primary mb-3 mb-md-0 ',
                        attr: {
            'id': 'newBooking',
          },

                        {{--  attr: {
            'data-bs-toggle': 'modal',
            'data-bs-target': '#DesignationModal'
          },
          init: function (api, node, config) {
            $(node).removeClass('btn-secondary');
          }  --}}
                    }],
                    // For responsive popup
                    responsive: {
                        details: {
                            display: $.fn.dataTable.Responsive.display.modal({
                                header: function(row) {
                                    var data = row.data();
                                    return 'Details of ' + data['name'];
                                }
                            }),
                            type: 'column',
                            renderer: function(api, rowIdx, columns) {
                                var data = $.map(columns, function(col, i) {
                                    return col.title !==
                                        '' // ? Do not show row in modal popup if title is blank (for check box)
                                        ?
                                        '<tr data-dt-row="' +
                                        col.rowIndex +
                                        '" data-dt-column="' +
                                        col.columnIndex +
                                        '">' +
                                        '<td>' +
                                        col.title +
                                        ':' +
                                        '</td> ' +
                                        '<td>' +
                                        col.data +
                                        '</td>' +
                                        '</tr>' :
                                        '';
                                }).join('');

                                return data ? $('<table class="table"/><tbody />').append(data) : false;
                            }
                        }
                    }

                });
            }




            // Filter form control to default size
            // ? setTimeout used for multilingual table initialization
            setTimeout(() => {
                $('.dataTables_filter .form-control').removeClass('form-control-sm');
                $('.dataTables_length .form-select').removeClass('form-select-sm');
            }, 300);





        });


    </script>



@endsection

@section('content')
    <h4 class="fw-semibold mb-4">Programmes</h4>



    <!-- Permission Table -->
    <div class="card event-form">
        <div class="card-datatable table-responsive">


            {{--  event booking form  --}}
            <div class="col-12">
                <div class="card">
                    <div
                        class="card-header sticky-element bg-label-primary d-flex justify-content-sm-between align-items-sm-center flex-column flex-sm-row">
                        <h5 class="card-title mb-sm-0 me-2"> Action Bar</h5>
                        <div class="action-btns">
                            <button class="btn btn-label-primary me-3">
                                <span class="align-middle"> Cancel</span>
                            </button>
                            <button class="btn btn-primary">
                                Save
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                      {{--  <div id="calendar"></div>  --}}
                        <div class="row">
                            <div class="col-lg-12 mx-auto">
                              <input type="hidden" id="event_id" value="0">

                                <div class="row g-3">
                                    <div class="col-md-2 col-12 mb-4">
                                        <label for="flatpickr-datetime" class="form-label">From Datetime</label>
                                        <input type="text" class="form-control flatpickr-datetime"
                                            placeholder="YYYY-MM-DD HH:MM" id="flatpickr-datetime-from" />
                                    </div>
                                    <div class="col-md-2 col-12 mb-4">
                                        <label for="flatpickr-datetime" class="form-label">TO Datetime</label>
                                        <input type="text" class="form-control flatpickr-datetime"
                                            placeholder="YYYY-MM-DD HH:MM" id="flatpickr-datetime-to" />
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label" for="event_name">Event Name</label>
                                        <input type="text" id="event_name" class="form-control "
                                            placeholder="658 799 8941" aria-label="658 799 8941" />
                                    </div>
                                    <div class="col-md-2 additional_details">
                                        <label class="form-label" for="alt-num">Hosted By</label>
                                        <select id="host" class="select2 form-select" data-allow-clear="true">
                                            <option value="" disabled>Select</option>
                                            <option value="1">CMD</option>
                                            <option value="2">External</option>

                                        </select>
                                    </div>

                                    <div class="col-md-2 additional_details">
                                        <label class="form-label" for="pincode">Venue Type</label>
                                        <select id="venue_type" class="select2 form-select" data-allow-clear="true">
                                            <option value="" disabled>Select</option>
                                            <option value="1">Inhouse</option>
                                            <option value="2">External</option>

                                        </select>
                                    </div>
                                    <div class="col-md-2 additional_details">
                                        <label class="form-label" for="no_of_participants">No of Participants</label>
                                        <input type="text" id="no_of_participants" class="form-control"
                                            placeholder="Nr. Wall Street" />
                                    </div>
                                    <div class="col-4 additional_details">
                                        <label class="form-label" for="external_dep">External Department</label>
                                        <input type="text" id="external_dep" class="form-control" placeholder="" />
                                    </div>
                                    <div class="col-md-2 additional_details">
                                        <label class="form-label" for="faculty">Faculty</label>
                                        <select id="faculty" class="select2 form-select" data-allow-clear="true">
                                            <option value="">Select</option>

                                        </select>
                                    </div>
                                    <div class="col-md-2 additional_details">
                                        <label class="form-label" for="coordinator">Coordinator</label>
                                        <select id="coordinator" class="select2 form-select" data-allow-clear="true">
                                            <option value="">Select</option>

                                        </select>
                                    </div>
                                    <hr class="additional_details">
                                    <div class="col-md-6 additional_details">
                                        <h5 class="my-4">Event Type</h5>
                                        <div class="row gy-3">
                                            <div class="col-md">
                                                <div class="form-check custom-option custom-option-icon">
                                                    <label class="form-check-label custom-option-content"
                                                        for="customRadioIcon1">
                                                        <span class="custom-option-body">
                                                            <i class='ti ti-briefcase'></i>
                                                            <span class="custom-option-title"> Online </span>
                                                            <small> Need video conferencing device. </small>
                                                        </span>
                                                        <input name="customRadioIcon" class="form-check-input"
                                                            type="radio" value="" id="customRadioIcon1"
                                                            checked />
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md">
                                                <div class="form-check custom-option custom-option-icon">
                                                    <label class="form-check-label custom-option-content"
                                                        for="customRadioIcon2">
                                                        <span class="custom-option-body">
                                                            <i class='ti ti-send'></i>
                                                            <span class="custom-option-title"> Offline </span>
                                                            <small>Without video conferencing device</small>
                                                        </span>
                                                        <input name="customRadioIcon" class="form-check-input"
                                                            type="radio" value="" id="customRadioIcon2" />
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md">
                                                <div class="form-check custom-option custom-option-icon">
                                                    <label class="form-check-label custom-option-content"
                                                        for="customRadioIcon3">
                                                        <span class="custom-option-body">
                                                            <i class='ti ti-crown'></i>
                                                            <span class="custom-option-title"> Hybrid </span>
                                                            <small> Need video conferencing device. </small>
                                                        </span>
                                                        <input name="customRadioIcon" class="form-check-input"
                                                            type="radio" value="" id="customRadioIcon3" />
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 additional_details">
                                        <h5 class="my-4">Room Seating Layout</h5>
                                        <div class="row gy-3">
                                            <div class="col-md mb-md-0 mb-2">
                                                <div
                                                    class="form-check custom-option custom-option-image custom-option-image-radio">
                                                    <label class="form-check-label custom-option-content"
                                                        for="customRadioImg1">
                                                        <span class="custom-option-body">
                                                            <img src="{{ asset('assets/img/backgrounds/speaker.png') }}"
                                                                alt="radioImg" />
                                                        </span>
                                                    </label>
                                                    <input name="customRadioImage" class="form-check-input"
                                                        type="radio" value="customRadioImg1" id="customRadioImg1"
                                                        checked />
                                                </div>
                                            </div>
                                            <div class="col-md mb-md-0 mb-2">
                                                <div
                                                    class="form-check custom-option custom-option-image custom-option-image-radio">
                                                    <label class="form-check-label custom-option-content"
                                                        for="customRadioImg2">
                                                        <span class="custom-option-body">
                                                            <img src="{{ asset('assets/img/backgrounds/airpods.png') }}"
                                                                alt="radioImg" />
                                                        </span>
                                                    </label>
                                                    <input name="customRadioImage" class="form-check-input"
                                                        type="radio" value="customRadioImg2" id="customRadioImg2" />
                                                </div>
                                            </div>
                                            <div class="col-md">
                                                <div
                                                    class="form-check custom-option custom-option-image custom-option-image-radio">
                                                    <label class="form-check-label custom-option-content"
                                                        for="customRadioImg3">
                                                        <span class="custom-option-body">
                                                            <img src="{{ asset('assets/img/backgrounds/headphones.png') }}"
                                                                alt="radioImg" />
                                                        </span>
                                                    </label>
                                                    <input name="customRadioImage" class="form-check-input"
                                                        type="radio" value="customRadioImg3" id="customRadioImg3" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <hr>
                                <div class="Availability mt-2">
                                    <div class="row">
                                        <div class="col-md-12 hallAvailabilityDetails">
                                            <input type="hidden" id="hallLastTransaction">
                                            <input type="hidden" id="resourseStart">
                                            <h5 class="custom_heading"><strong>Hall Availability</strong></h5>
                                            <div id="hallAvailabilityCalendar"></div>
                                        </div>

                                    </div>

                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{--  end  --}}
        </div>
    </div>

    <div class="card event-list">
      <div class="card-datatable table-responsive">
        <table class="datatables-designation table border-top">
          <thead>
              <tr>
                  <th></th>
                  <th>Programme</th>
                  <th>Host</th>
                  <th>Type</th>
                  <th>From Time</th>
                  <th>To Time</th>
                  <th>Requested_at</th>
                  <th>Status</th>
                  <th>Action By</th>
                  <th>Actions</th>

              </tr>
          </thead>
      </table>
      </div>
    </div>

    <!-- Modal -->
    @include('_partials/_modals/modal-misspunch')
    <!-- /Modal -->
@endsection
