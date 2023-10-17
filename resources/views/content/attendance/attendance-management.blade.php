@extends('layouts/layoutMaster')

@section('title', 'Designation - Master')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/formvalidation/dist/css/formValidation.min.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/animate-css/animate.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/sweetalert2/sweetalert2.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/jquery-timepicker/jquery-timepicker.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/dropzone/dropzone.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}" />


@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js')}}"></script>

<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/FormValidation.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/sweetalert2/sweetalert2.js')}}"></script>
<script src="{{asset('assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.js')}}" ></script>
<script src="{{asset('assets/vendor/libs/moment/moment.js')}}"></script>
<script src="{{asset('assets/vendor/libs/jquery-timepicker/jquery-timepicker.js')}}"></script>
<script src="{{asset('assets/vendor/libs/dropzone/dropzone.js')}}"></script>
<script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>

@endsection

@section('page-script')
<script src="{{asset('assets/js/forms-file-upload.js')}}"></script>
<script>


$(function () {
  var dataTablePermissions = $('.datatables-designation'),
    dt_permission,
    permissionList = baseUrl + 'movement/list';


    $(".datepicker").datepicker({
      autoclose: true ,
      });
       // Select2 Country
  var select2 = $('.select2');
  if (select2.length) {
    select2.each(function () {
      var $this = $(this);
      $this.wrap('<div class="position-relative"></div>').select2({
        placeholder: 'Select value',
        dropdownParent: $this.parent()
      });
    });
  }

  // ajax setup
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  // Users List datatable





// Add/Edit designation form validation
document.addEventListener('DOMContentLoaded', function (e) {
  (function () {
    FormValidation.formValidation(document.getElementById('designationForm'), {
      fields: {
        employment_type: {
          validators: {
            notEmpty: {
              message: 'Please select Employment Type'
            }
          }
        },
        modalDesignationName: {
          validators: {
            notEmpty: {
              message: 'Please enter designation name'
            }
          }
        }
      },
      plugins: {
        trigger: new FormValidation.plugins.Trigger(),
        bootstrap5: new FormValidation.plugins.Bootstrap5({
          // Use this for enabling/changing valid/invalid class
          // eleInvalidClass: '',
          eleValidClass: '',
          rowSelector: '.col-sm-9'
        }),
        submitButton: new FormValidation.plugins.SubmitButton(),
        // Submit the form when all fields are valid
        // defaultSubmit: new FormValidation.plugins.DefaultSubmit(),
        autoFocus: new FormValidation.plugins.AutoFocus()
      }
    });
  })();
});

(function () {
  // On edit permission click, update text
  var designationSubmit = document.querySelector('#report');


    $('#DesignationModal').on('hidden.bs.modal', function (e) {
      $(this)
        .find("input,textarea,select")
           .val('')
           .end()
        .find("input[type=checkbox], input[type=radio]")
           .prop("checked", "")
           .end();
    })

    designationSubmit.onclick = function () {

      var  title =  $("#eventTitle").val();
      var  type =  $("#eventLabel").val();
      var  start_date =  $("#fromDate").val();
      var  start_time =  $("#fromTime").val();
      var  end_date =  $("#toDate").val();
      var  end_time =  $("#toTime").val();
      var  location =  $("#eventLocation").val();
      var  description =  $("#eventDescription").val();
      var request_type =   $("#submit_designation").data('type');
      var desig_id =   $("#submit_designation").data('id');


          $.ajax({
            data:  {
              title:title,
              type:type,
              start_date:start_date,
              start_time:start_time,
              end_date:end_date,
              end_time:end_time,
              location:location,
              description:description,
              "_token": "{{ csrf_token() }}",
          },
            url: `${baseUrl}movement/store`,
            type: 'POST',

            success: function (status) {

                $('#DesignationModal').modal('hide');
              // sweetalert
              Swal.fire({
                icon: 'success',
                title: `Successfully ${status}!`,
                text: `Movement ${status} Successfully.`,
                customClass: {
                  confirmButton: 'btn btn-success'
                }
              });

            },
            error: function (err) {
              $('#DesignationModal').modal('hide');
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




})();


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
<h4 class="fw-semibold mb-4">Attendance Management</h4>


<div class="row">
  <div class="col">
    <h6 class="mt-4">Import Attendance & Generate Report </h6>
    <div class="card mb-3">
      <div class="card-header">
        <ul class="nav nav-tabs card-header-tabs" role="tablist">
          <li class="nav-item  ">
            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#form-tabs-personal" role="tab" aria-selected="true">Generate Report</button>
          </li>
          <li class="nav-item">
            <button class="nav-link " data-bs-toggle="tab" data-bs-target="#form-tabs-account" role="tab" aria-selected="false">Attendance Update</button>
          </li>

        </ul>
      </div>

      <div class="tab-content">
        <div class="tab-pane fade active show" id="form-tabs-personal" role="tabpanel">
          {{--  <form>  --}}
            <div class="row g-3">
              <div class="col-md-6">
                <label for="fromDate" class="form-label">From</label>
            <input type="text" class="form-control datepicker" id="fromDate" name="fromDate" placeholder="MM/DD/YYYY" class="form-control" />

              </div>
              <div class="col-md-6">
                <label for="toDate" class="form-label">To</label>
            <input type="text" class="form-control datepicker" id="toDate" name="toDate" placeholder="MM/DD/YYYY" class="form-control" />

              </div>
              <div class="col-md-6">
                <label class="form-label" for="formtabs-country">Employment Type</label>
                <select id="formtabs-country" class="select2 form-select" multiple data-allow-clear="true">
                  <option value="">Select All</option>
                  @foreach ($employment_types as $item)
                  <option value={{$item->id}}>{{$item->employment_type}}</option>
                  @endforeach


                </select>
              </div>
              <div class="col-md-6 select2-primary">
                <label class="form-label" for="formtabs-language">Employee</label>
                <select id="formtabs-language" class="select2 form-select" multiple>
                  <option value="">Select All</option>
                  @foreach ($employees as $item)
                  <option value={{$item->id}}>{{$item->name}}</option>
                  @endforeach
                </select>
              </div>

            </div>
            <div class="pt-4">
              <a type="submit" href="/downloadBulk"  class="btn btn-primary me-sm-3 me-1">Generate</a>
              <button type="reset" class="btn btn-label-secondary">Cancel</button>
            </div>
          {{--  </form>  --}}
        </div>
        <div class="tab-pane fade" id="form-tabs-account" role="tabpanel">
          <form>
            <div class="row">
              <!-- Basic  -->
              <div class="col-12">
                <div class="card mb-4">
                  <h5 class="card-header">Attendance Import</h5>
                  <div class="card-body">
                    <form action="/upload" class="dropzone needsclick" id="dropzone-basic">
                      <div class="dz-message needsclick">
                        Drop files here or click to upload
                        <span class="note needsclick">(This is just a demo dropzone. Selected files are <strong>not</strong> actually uploaded.)</span>
                      </div>
                      <div class="fallback">
                        <input name="file" type="file" />
                      </div>
                    </form>
                  </div>
                </div>
              </div>
            <div class="pt-4">
              <button type="submit" class="btn btn-primary me-sm-3 me-1">Import</button>
              <button type="reset" class="btn btn-label-secondary">Cancel</button>
            </div>
          </form>
        </div>

      </div>

    </div>
  </div>
</div>


<!-- Modal -->

<!-- /Modal -->
@endsection
