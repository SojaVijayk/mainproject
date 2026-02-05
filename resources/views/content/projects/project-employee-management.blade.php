@extends('layouts/layoutMaster')

@section('title', 'User List - Pages')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/formvalidation/dist/css/formValidation.min.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/animate-css/animate.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/sweetalert2/sweetalert2.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/bs-stepper/bs-stepper.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/bootstrap-select/bootstrap-select.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/dropzone/dropzone.css')}}" />


@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/moment/moment.js')}}"></script>
<script src="{{asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js')}}"></script>
<script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/FormValidation.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/cleavejs/cleave.js')}}"></script>
<script src="{{asset('assets/vendor/libs/cleavejs/cleave-phone.js')}}"></script>
<script src="{{asset('assets/vendor/libs/sweetalert2/sweetalert2.js')}}"></script>
<script src="{{asset('assets/vendor/libs/bs-stepper/bs-stepper.js')}}"></script>
<script src="{{asset('assets/vendor/libs/bootstrap-select/bootstrap-select.js')}}"></script>
<script src="{{asset('assets/vendor/libs/dropzone/dropzone.js')}}" ></script>
@endsection

@section('page-script')
<script src="{{asset('assets/js/form-wizard-icons.js')}}"></script>3
<script src="{{asset('assets/js/forms-file-upload.js')}}"></script>
<script>
  /**
 * Page User List
 */

'use strict';

// Datatable (jquery)
$(function () {
  let borderColor, bodyBg, headingColor;

  if (isDarkStyle) {
    borderColor = config.colors_dark.borderColor;
    bodyBg = config.colors_dark.bodyBg;
    headingColor = config.colors_dark.headingColor;
  } else {
    borderColor = config.colors.borderColor;
    bodyBg = config.colors.bodyBg;
    headingColor = config.colors.headingColor;
  }

  // Variable declaration for table
  var dt_user_table = $('.datatables-users'),userView,select2,statusObj, offCanvasForm = $('#offcanvasAddUser'),
      usersList = @if(isset($is_global) && $is_global) baseUrl + 'pms/employees/list' @else baseUrl + 'project/employees/detail/list' @endif,

    select2 = $('.select2'),
    userView = baseUrl + 'project/employee/view/account',
    userDetails = baseUrl + 'pms/employees/details',

    statusObj = {
      1: { title: 'Active', class: 'bg-label-success' },
      2: { title: 'Inactive', class: 'bg-label-secondary' }
    };





  // ajax setup
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });



  if (select2.length) {
    var $this = select2;
    $this.wrap('<div class="position-relative"></div>').select2({
      placeholder: 'Select Privilege',
      dropdownParent: $this.parent()
    });
  }

  // Users datatable
  if (dt_user_table.length) {
   var dt_user = dt_user_table.DataTable({

      ajax: {
        url: "{{ route('pms.employees.list') }}"
       },
      columns: [
        // columns according to JSON
        { data: '' },
        { data: 'name' },
        { data: 'email' },
        { data: 'age' },
        { data: 'mobile' },
        { data: 'dob' },
        { data: 'date_of_joining' },
        { data: 'designation' },
        { data: 'address' },
        { data: 'action' }
      ],
      columnDefs: [
        {
          // For Responsive
          className: 'control',
          searchable: false,
          orderable: false,
          responsivePriority: 2,
          targets: 0,
          render: function (data, type, full, meta) {
            return '';
          }
        },
        {
          // User full name and email
          targets: 1,
          responsivePriority: 4,
          render: function (data, type, full, meta) {
            var $name = full['name'],
              $email = full['email'],
              $image = ''; // Removed profile_pic support as column is gone
            
            // For Avatar badge
            var stateNum = Math.floor(Math.random() * 6);
            var states = ['success', 'danger', 'warning', 'info', 'primary', 'secondary'];
            var $state = states[stateNum],
              $initials = $name.match(/\b\w/g) || [];
            $initials = (($initials.shift() || '') + ($initials.pop() || '')).toUpperCase();
            var $output = '<span class="avatar-initial rounded-circle bg-label-' + $state + '">' + $initials + '</span>';

            // Creates full output for row
            var $row_output =
              '<div class="d-flex justify-content-start align-items-center user-name">' +
              '<div class="avatar-wrapper">' +
              '<div class="avatar avatar-sm me-3">' +
              $output +
              '</div>' +
              '</div>' +
              '<div class="d-flex flex-column">' +
           
              '<span class="fw-semibold">' +
              $name +
              '</span>' +
              '<small class="text-muted">' +
              $email +
              '</small>' +
              '</div>' +
              '</div>';
            return $row_output;
          }
        },
        {
          // Actions
          targets: -1,
          title: 'Actions',
          searchable: false,
          orderable: false,
          render: function (data, type, full, meta) {
            var detailUrl = "{{ route('pms.employees.details', ':id') }}";
            detailUrl = detailUrl.replace(':id', full['id']);
            
            return (
              '<div class="d-flex align-items-center">' +
              '<a href="javascript:;" class="text-body deleted-record"><i class="ti ti-trash ti-sm me-2"></i></a>' +
              '<a href="javascript:;" class="text-body dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="ti ti-dots-vertical ti-sm mx-1"></i></a>' +
              '<div class="dropdown-menu dropdown-menu-end m-0">' +
              '<a href="javascript:;" class="dropdown-item edit-record" data-id="' + full['id'] + '">Edit</a>' +
              '<a href="' + detailUrl + '" class="dropdown-item">View Details</a>' +
              '</div>' +
              '</div>'
            );
          }
        }
      ],
      order: [[1, 'desc']],
      dom:
        '<"row me-2"' +
        '<"col-md-2"<"me-3"l>>' +
        '<"col-md-10"<"dt-action-buttons text-xl-end text-lg-start text-md-end text-start d-flex align-items-center justify-content-end flex-md-row flex-column mb-3 mb-md-0"fB>>' +
        '>t' +
        '<"row mx-2"' +
        '<"col-sm-12 col-md-6"i>' +
        '<"col-sm-12 col-md-6"p>' +
        '>',
      language: {
        sLengthMenu: '_MENU_',
        search: '',
        searchPlaceholder: 'Search..'
      },
      // Buttons with Dropdown
      buttons: [
        {
          extend: 'collection',
          className: 'btn btn-label-secondary dropdown-toggle mx-3',
          text: '<i class="ti ti-screen-share me-1 ti-xs"></i>Export',
          buttons: [
            {
              extend: 'print',
              text: '<i class="ti ti-printer me-2" ></i>Print',
              className: 'dropdown-item',
              exportOptions: {
                columns: [1, 2, 3, 4, 5],
                // prevent avatar to be print
                format: {
                  body: function (inner, coldex, rowdex) {
                    if (inner.length <= 0) return inner;
                    var el = $.parseHTML(inner);
                    var result = '';
                    $.each(el, function (index, item) {
                      if (item.classList !== undefined && item.classList.contains('user-name')) {
                        result = result + item.lastChild.firstChild.textContent;
                      } else if (item.innerText === undefined) {
                        result = result + item.textContent;
                      } else result = result + item.innerText;
                    });
                    return result;
                  }
                }
              },
              customize: function (win) {
                //customize print view for dark
                $(win.document.body)
                  .css('color', headingColor)
                  .css('border-color', borderColor)
                  .css('background-color', bodyBg);
                $(win.document.body)
                  .find('table')
                  .addClass('compact')
                  .css('color', 'inherit')
                  .css('border-color', 'inherit')
                  .css('background-color', 'inherit');
              }
            },
            {
              extend: 'csv',
              text: '<i class="ti ti-file-text me-2" ></i>Csv',
              className: 'dropdown-item',
              exportOptions: {
                columns: [1, 2, 3, 4, 5],
                // prevent avatar to be display
                format: {
                  body: function (inner, coldex, rowdex) {
                    if (inner.length <= 0) return inner;
                    var el = $.parseHTML(inner);
                    var result = '';
                    $.each(el, function (index, item) {
                      if (item.classList !== undefined && item.classList.contains('user-name')) {
                        result = result + item.lastChild.firstChild.textContent;
                      } else if (item.innerText === undefined) {
                        result = result + item.textContent;
                      } else result = result + item.innerText;
                    });
                    return result;
                  }
                }
              }
            },
            {
              extend: 'excel',
              text: '<i class="ti ti-file-spreadsheet me-2"></i>Excel',
              className: 'dropdown-item',
              exportOptions: {
                columns: [1, 2, 3, 4, 5],
                // prevent avatar to be display
                format: {
                  body: function (inner, coldex, rowdex) {
                    if (inner.length <= 0) return inner;
                    var el = $.parseHTML(inner);
                    var result = '';
                    $.each(el, function (index, item) {
                      if (item.classList !== undefined && item.classList.contains('user-name')) {
                        result = result + item.lastChild.firstChild.textContent;
                      } else if (item.innerText === undefined) {
                        result = result + item.textContent;
                      } else result = result + item.innerText;
                    });
                    return result;
                  }
                }
              }
            },
            {
              extend: 'pdf',
              text: '<i class="ti ti-file-code-2 me-2"></i>Pdf',
              className: 'dropdown-item',
              exportOptions: {
                columns: [1, 2, 3, 4, 5],
                // prevent avatar to be display
                format: {
                  body: function (inner, coldex, rowdex) {
                    if (inner.length <= 0) return inner;
                    var el = $.parseHTML(inner);
                    var result = '';
                    $.each(el, function (index, item) {
                      if (item.classList !== undefined && item.classList.contains('user-name')) {
                        result = result + item.lastChild.firstChild.textContent;
                      } else if (item.innerText === undefined) {
                        result = result + item.textContent;
                      } else result = result + item.innerText;
                    });
                    return result;
                  }
                }
              }
            },
            {
              extend: 'copy',
              text: '<i class="ti ti-copy me-2" ></i>Copy',
              className: 'dropdown-item',
              exportOptions: {
                columns: [1, 2, 3, 4, 5],
                // prevent avatar to be display
                format: {
                  body: function (inner, coldex, rowdex) {
                    if (inner.length <= 0) return inner;
                    var el = $.parseHTML(inner);
                    var result = '';
                    $.each(el, function (index, item) {
                      if (item.classList !== undefined && item.classList.contains('user-name')) {
                        result = result + item.lastChild.firstChild.textContent;
                      } else if (item.innerText === undefined) {
                        result = result + item.textContent;
                      } else result = result + item.innerText;
                    });
                    return result;
                  }
                }
              }
            }
          ]
        },
        {{--  {
          text: '<i class="ti ti-plus me-0 me-sm-1 ti-xs"></i><span class="d-none d-sm-inline-block">Add New User</span>',
          className: 'add-new btn btn-primary',
          attr: {
            'data-bs-toggle': 'offcanvas',
            'data-bs-target': '#offcanvasAddUser'
          }
        },  --}}
      ],
      // For responsive popup
      responsive: {
        details: {
          display: $.fn.dataTable.Responsive.display.modal({
            header: function (row) {
              var data = row.data();
              return 'Details of ' + data['name'];
            }
          }),
          type: 'column',
          renderer: function (api, rowIdx, columns) {
            var data = $.map(columns, function (col, i) {
              return col.title !== '' // ? Do not show row in modal popup if title is blank (for check box)
                ? '<tr data-dt-row="' +
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
                    '</tr>'
                : '';
            }).join('');

            return data ? $('<table class="table"/><tbody />').append(data) : false;
          }
        }
      },
      initComplete: function () {
        // Adding role filter once table initialized
        this.api()
          .columns(3)
          .every(function () {
            var column = this;
            var select = $(
              '<select id="UserRole" class="form-select text-capitalize"><option value=""> Select User Type </option></select>'
            )
              .appendTo('.user_role')
              .on('change', function () {
                var val = $.fn.dataTable.util.escapeRegex($(this).val());
                column.search(val ? '^' + val + '$' : '', true, false).draw();
              });

            column
              .data()
              .unique()
              .sort()
              .each(function (d, j) {
                select.append('<option value="' + d + '">' + d + '</option>');
              });
          });
        // Adding plan filter once table initialized
        this.api()
          .columns(4)
          .every(function () {
            var column = this;
            var select = $(
              '<select id="UserPlan" class="form-select text-capitalize"><option value=""> Select Designation </option></select>'
            )
              .appendTo('.user_plan')
              .on('change', function () {
                var val = $.fn.dataTable.util.escapeRegex($(this).val());
                column.search(val ? '^' + val + '$' : '', true, false).draw();
              });

            column
              .data()
              .unique()
              .sort()
              .each(function (d, j) {
                select.append('<option value="' + d + '">' + d + '</option>');
              });
          });
        // Adding status filter once table initialized
        this.api()
          .columns(5)
          .every(function () {
            var column = this;
            var select = $(
              '<select id="FilterTransaction" class="form-select text-capitalize"><option value=""> Select Status </option></select>'
            )
              .appendTo('.user_status')
              .on('change', function () {
                var val = $.fn.dataTable.util.escapeRegex($(this).val());
                column.search(val ? '^' + val + '$' : '', true, false).draw();
              });

            column
              .data()
              .unique()
              .sort()
              .each(function (d, j) {
                select.append(
                  '<option value="' +
                    statusObj[d].title +
                    '" class="text-capitalize">' +
                    statusObj[d].title +
                    '</option>'
                );
              });
          });
      }
    });
  }

  // Delete Record
  $('.datatables-users tbody').on('click', '.delete-record', function () {
    dt_user.row($(this).parents('tr')).remove().draw();
  });

  // Filter form control to default size
  // ? setTimeout used for multilingual table initialization
  setTimeout(() => {
    $('.dataTables_filter .form-control').removeClass('form-control-sm');
    $('.dataTables_length .form-select').removeClass('form-select-sm');
  }, 300);
});

// Validation & Phone mask
document.addEventListener('DOMContentLoaded', function () {
// Validation & Phone mask

  const addGlobalEmployeeForm = document.getElementById('addGlobalEmployeeForm');

  // Add Global Employee Form Validation
  const fv = FormValidation.formValidation(addGlobalEmployeeForm, {
    fields: {
      name: {
        validators: {
          notEmpty: {
            message: 'Please enter fullname'
          }
        }
      },
      email: {
        validators: {
          notEmpty: {
            message: 'Please enter your email'
          },
          emailAddress: {
            message: 'The value is not a valid email address'
          }
        }
      },
      mobile: {
        validators: {
          notEmpty: {
            message: 'Please enter your contact number'
          },
          regexp: {
            regexp: /^[0-9]+$/,
            message: 'The value is not a valid number'
          }
        }
      },
      age: {
        validators: {
          notEmpty: {
            message: 'Please enter age'
          }
        }
      },
      dob: {
        validators: {
          notEmpty: {
            message: 'Please select Date of Birth'
          }
        }
      },
      joining_date: {
        validators: {
          notEmpty: {
            message: 'Please select Date of Joining'
          }
        }
      },
      designation: {
        validators: {
          notEmpty: {
            message: 'Please select designation'
          }
        }
      },
      address: {
        validators: {
          notEmpty: {
            message: 'Please enter address'
          }
        }
      }
    },
    plugins: {
      trigger: new FormValidation.plugins.Trigger(),
      bootstrap5: new FormValidation.plugins.Bootstrap5({
        // Use this for enabling/changing valid/invalid class
        eleValidClass: '',
        rowSelector: function (field, ele) {
          // field is the field name & ele is the field element
          return '.col-sm-6, .col-sm-12';
        }
      }),
      autoFocus: new FormValidation.plugins.AutoFocus()
    }
  });

  // Manual Submit Handler
  const submitBtn = document.getElementById('btn-submit-employee');
  if(submitBtn){
    submitBtn.addEventListener('click', function (e) {
      e.preventDefault();
      fv.validate().then(function(status) {
        if (status === 'Valid') {
          let formData = $('#addGlobalEmployeeForm').serialize();
          $.ajax({
            data: formData,
            url: `{{ route('pms.employees.store') }}`,
            type: 'POST',
            success: function (status) {
              $('#fullscreenModal').modal('hide');
              Swal.fire({
                title: 'Success!',
                text: 'Employee details submitted successfully',
                icon: 'success',
                customClass: {
                  confirmButton: 'btn btn-primary'
                },
                buttonsStyling: false
              }).then(function (result) {
               
                  window.location.reload();
               
              });
            },
            error: function (err) {
              Swal.fire({
                title: 'Error!',
                text: err.responseJSON && err.responseJSON.message ? err.responseJSON.message : 'Something went wrong.',
                icon: 'error',
                customClass: {
                  confirmButton: 'btn btn-success'
                }
              });
            }
          });
        }
      });
    });
  }

  // Edit Record
  $(document).on('click', '.edit-record', function () {
    var id = $(this).data('id');
    var editUrl = baseUrl + 'pms/employees/edit/' + id;
    var updateUrl = baseUrl + 'pms/employees/update-master/' + id;

    // Fetch data
    $.get(editUrl, function (data) {
      if (data) {
        // Populate modal
        $('#wizard_name').val(data.name);
        $('#wizard_email').val(data.email);
        $('#wizard_mobile').val(data.mobile);
        $('#wizard_age').val(data.age);
        $('#wizard_dob').val(data.dob);
        $('#wizard_joining_date').val(data.date_of_joining);
        $('#wizard_address').val(data.address);
        $('#wizard_designation').val(data.designation_id);

        // Update form action
        $('#addGlobalEmployeeForm').attr('action', updateUrl);
        $('#modalFullTitle').text('Edit User');
        $('#btn-submit-employee').text('Update');

        // Show modal
        var myModal = new bootstrap.Modal(document.getElementById('fullscreenModal'));
        myModal.show();
      }
    });
  });

  // Reset modal on close
  $('#fullscreenModal').on('hidden.bs.modal', function () {
      $('#addGlobalEmployeeForm')[0].reset();
      $('#addGlobalEmployeeForm').attr('action', "{{ route('pms.employees.store') }}");
      $('#modalFullTitle').text('Add New User');
      $('#btn-submit-employee').text('Submit');
  });

});

</script>
@endsection

@section('content')
<div class="col-md-12 mb-4">
  <div class="card">
    <div class="card-body">
      <div class="divider">
        <div class="divider-text text-primary">
          @if(isset($project_details))
            Employee Details of {{$project_details->project_name}} Project
            <input type="hidden" name="project_id" value={{$project_details->id}} />
          @else
            Employee Management
          @endif
        </div>
      </div>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-header border-bottom d-flex justify-content-between align-items-center">
    <h5 class="card-title mb-0">Employee Management</h5>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#fullscreenModal">
      <i class="ti ti-plus me-0 me-sm-1 ti-xs"></i> Add Employee
    </button>
  </div>
  <div class="card-datatable table-responsive">


    <table class="datatables-users table border-top">
      <thead>
        <tr>
          <th></th>
          <th>Name</th>
          <th>Email</th>
          <th>Age</th>
          <th>Mobile</th>
          <th>DOB</th>
          <th>DOJ</th>
          <th>Designation</th>
          <th>Address</th>
          <th>Actions</th>
        </tr>
      </thead>
    </table>
  </div>
  <!-- Offcanvas to add new user -->
  <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasAddUser" aria-labelledby="offcanvasAddUserLabel">
    <div class="offcanvas-header">
      <h5 id="offcanvasAddUserLabel" class="offcanvas-title">Add User</h5>
      <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
      <form class="add-new-user pt-0" id="addNewUserForm" onsubmit="return false">
        <div class="mb-3">
          <label class="form-label" for="name">Full Name</label>
          <input type="text" class="form-control" id="name" placeholder="John Doe" name="name" aria-label="John Doe" />
        </div>
        <div class="mb-3">
          <label class="form-label" for="email">Email</label>
          <input type="text" id="email" class="form-control" placeholder="john.doe@example.com" aria-label="john.doe@example.com" name="email" />
        </div>
        <div class="mb-3">
          <label class="form-label" for="mobile">Contact</label>
          <input type="text" id="mobile" class="form-control phone-mask" placeholder="609 988 44 11" aria-label="john.doe@example.com" name="mobile" />
        </div>
        <div class="mb-3">
          <label class="form-label" for="empId">Employee ID</label>
          <input type="text" id="empId" class="form-control " placeholder="XXX" aria-label="john.doe@example.com" name="empId" />
        </div>
        <div class="mb-3">
          <label class="form-label" for="designation">Designation</label>
          <select id="designation" name="designation" class=" form-select">
            <option disabled value="">Select</option>
            @foreach ($designations as $designation)
            <option value={{$designation->id}}> {{$designation->designation}}</option>
            @endforeach


          </select>
        </div>
        <div class="mb-3">
          <label class="form-label" for="usertype_role">User Role</label>
          <select id="usertype_role" name="usertype_role" class="form-select">
            @foreach ($user_types as $usertype)
            <option value="{{$usertype->id}}"> {{$usertype->usertype_role}}</option>
            @endforeach
          </select>
        </div>

        <button type="submit" class="btn btn-primary me-sm-3 me-1 data-submit">Submit</button>
        <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>
      </form>



    </div>
  </div>


  <div class="modal fade" id="fullscreenModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalFullTitle">Add New User</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <!-- Vertical Icons Wizard -->
          <div class="col-12 mb-4">
             <form id="addGlobalEmployeeForm" method="POST" action="{{ route('pms.employees.store') }}" onSubmit="return false">
                @csrf
                <div class="row g-3">
                  <div class="col-sm-6">
                    <label class="form-label" for="name">Full Name</label>
                    <input type="text" id="wizard_name" name="name" class="form-control" placeholder="John" required />
                  </div>
                  <div class="col-sm-6">
                    <label class="form-label" for="email">Email</label>
                    <input type="email" id="wizard_email" name="email" class="form-control" placeholder="john@example.com" required />
                  </div>
                  <div class="col-sm-6">
                    <label class="form-label" for="age">Age</label>
                    <input type="number" id="wizard_age" name="age" class="form-control" placeholder="25" required />
                  </div>
                  <div class="col-sm-6">
                    <label class="form-label" for="mobile">Mobile</label>
                    <input type="text" id="wizard_mobile" name="mobile" class="form-control phone-mask" placeholder="1234567890" required />
                  </div>
                  <div class="col-sm-6">
                    <label class="form-label" for="dob">Date of Birth</label>
                    <input type="date" id="wizard_dob" name="dob" class="form-control" required />
                  </div>
                  <div class="col-sm-6">
                    <label class="form-label" for="joining_date">Date of Joining</label>
                    <input type="date" id="wizard_joining_date" name="joining_date" class="form-control" required />
                  </div>
                  <div class="col-sm-6">
                    <label class="form-label" for="designation">Designation</label>
                    <select id="wizard_designation" name="designation" class="form-select" required>
                      @foreach ($designations as $designation)
                        <option value="{{$designation->id}}">{{$designation->designation}}</option>
                      @endforeach
                    </select>
                  </div>
                  <div class="col-sm-12">
                    <label class="form-label" for="address">Address</label>
                    <textarea id="wizard_address" name="address" class="form-control" rows="2" placeholder="123 Main St..." required></textarea>
                  </div>
                  <div class="col-12 text-center mt-4">
                    <button type="submit" id="btn-submit-employee" class="btn btn-primary me-sm-3 me-1 btn-submit">Submit</button>
                    <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancel</button>
                  </div>
                </div>
            </form>
          </div>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
          {{--  <button type="button" class="btn btn-primary">Save changes</button>  --}}
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
