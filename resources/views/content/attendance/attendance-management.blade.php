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

<style>
  #loading-overlay {
    position: absolute;
    width: 100%;
    height:100%;
    left: 0;
    top: 0;
    display: none;
    align-items: center;
    background-color: #000;
    z-index: 999;
    opacity: 0.5;
}
.loading-icon{ position:absolute;border-top:2px solid #fff;border-right:2px solid #fff;border-bottom:2px solid #fff;border-left:2px solid #767676;border-radius:25px;width:25px;height:25px;margin:0 auto;position:absolute;left:50%;margin-left:-20px;top:50%;margin-top:-20px;z-index:4;-webkit-animation:spin 1s linear infinite;-moz-animation:spin 1s linear infinite;animation:spin 1s linear infinite;}
@-moz-keyframes spin { 100% { -moz-transform: rotate(360deg); } }
@-webkit-keyframes spin { 100% { -webkit-transform: rotate(360deg); } }
@keyframes spin { 100% { -webkit-transform: rotate(360deg); transform:rotate(360deg); } }
</style>
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

    $("body").on("click","#import", function (e) {
      e.preventDefault();
      $("#loading-overlay").show();

      var fd = new FormData();
      var files = $('input[type=file]')[0].files[0];
      fd.append('file',files);
      $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
              }
          })
          $.ajax({
              type: "POST",
              url: '/attendance/import',
              data: fd,
              contentType: false,
              processData: false,
              // dataType: 'json',
              success: function (data) {
                $("#loading-overlay").hide();
                Swal.fire({
                  icon: 'success',
                  title: `Successfully Imported!`,
                  {{--  text: `Designation ${status} Successfully.`,  --}}
                  customClass: {
                    confirmButton: 'btn btn-success'
                  }
                });

              },
              error: function(data){
                $("#loading-overlay").hide();
                Swal.fire({
                  title: 'Oh Sorry!',
                  text: 'Something went wrong',
                  icon: 'error',
                  customClass: {
                    confirmButton: 'btn btn-success'
                  }
                });
              }
          });
  });

  $("body").on("click","#report", function (e) {
    e.preventDefault();

      var  fromDate =  $("#fromDate").val();
      var  toDate =  $("#toDate").val();
      var  employeeList =  $("#employeeList").val();
      {{--  var  view_type =  $("#viewTypeOptinon").val();  --}}
      var  view_type = $('input[name="viewTypeOptinon"]:checked').val();
      var  reportType =  $("#reportType").val();



      $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
        }
    })

    if(reportType ==1){

      if(view_type == 'excel' || view_type == 'pdf'){

        $.ajax({
             data:  {
              fromDate:fromDate,
                toDate:toDate,
                type:'2',
                view_type:view_type,
                employeeList:employeeList,
                "_token": "{{ csrf_token() }}",
            },
              url: `${baseUrl}downloadBulk`,
              type: 'POST',
              xhrFields:{
                responseType: 'blob'
            },
            beforeSend: function() {
                //
            },
            success: function(data) {
                var url = window.URL || window.webkitURL;
                var objectUrl = url.createObjectURL(data);
                window.open(objectUrl);
            },
            error: function(data) {
                //
            }
        });
        }
        else{



          $.ajax({
            data:  {
             fromDate:fromDate,
               toDate:toDate,
               type:'2',
               view_type:view_type,
               employeeList:employeeList,
               "_token": "{{ csrf_token() }}",
           },
             url: `${baseUrl}downloadBulk`,
             type: 'POST',

           success: function(data) {
            var tbody='';
            data.list.forEach((item, index) => {
              tbody=tbody+'<tr><td>'+item.name+'</td><td>'+item.date+'</td>'+
               ' <td> <span class="text-'+(item.in_time <= '09:30'  ? "success" : 'warning')+'">'+(item.in_time != null ? item.in_time : '')+'</span></td>'+
               '<td><span class="text-'+(item.out_time >= '17:30'  ? "success" : 'warning')+'">'+(item.out_time != null ? item.out_time : '')+'</span></td>'+
               '';
                var leave= '';
                var leave_status= '';
                var leave_day= '';

                var mov_title= '';
                var mov_loc= '';
                var mov_date= '';
                var mov_type= '';
                var mov_status= '';


                if((item.leave_type != '') && (item.leave_type != null)){
                  leave = item.leave_type;
                  leave_status=(item.leave_status == 1 ? 'Approved' : (item.leave_status == 2 ? 'Rejected' : 'Pending'));
                  leave_day= (item.leave_day_type == 1 ? 'Full Day' : (item.leave_day_type == 2 ? 'AN' : 'FN'));

                  tbody=tbody+'<td> Leave Details (Leave Type : '+leave+' - Day Type :'+leave_day+ ' - Status:' + leave_status+ ')</td>';
                }



                if((item.movement_status != '') && (item.movement_status != null)){
                  mov_type = item.type;
                  mov_title= item.title;
                  mov_loc= item.location;
                  mov_date = item.start_date+' - '+item.start_time+' to '+item.end_date+' - '+item.end_time;
                  mov_status=(item.mov_status == 1 ? 'Approved' : (item.mov_status == 2 ? 'Rejected' : 'Pending'));
                  tbody=tbody+'<td> Movement Details ( '+mov_type+' -  '+mov_title+ ' Duraton : '+mov_date+' - Status:' + mov_status+ ')</td>';

                }

                tbody=tbody+'</tr>';

              });
              $('#DesignationModal').modal('show');
            $(".datatables-leave-list #dataList").html(tbody);

           },
           error: function(data) {
               //
           }
       });

        }
    }
    else if(reportType ==2){
      if(view_type == 'excel' || view_type == 'pdf'){

        $.ajax({
             data:  {
              fromDate:fromDate,
                toDate:toDate,
                type:'2',
                view_type:view_type,
                employeeList:employeeList,
                "_token": "{{ csrf_token() }}",
            },
              url: `${baseUrl}movement/downloadBulk`,
              type: 'POST',
              xhrFields:{
                responseType: 'blob'
            },
            beforeSend: function() {
                //
            },
            success: function(data) {
                var url = window.URL || window.webkitURL;
                var objectUrl = url.createObjectURL(data);
                window.open(objectUrl);
            },
            error: function(data) {
                //
            }
        });
        }
        else{



          $.ajax({
            data:  {
             fromDate:fromDate,
               toDate:toDate,
               type:'2',
               view_type:view_type,
               employeeList:employeeList,
               "_token": "{{ csrf_token() }}",
           },
             url: `${baseUrl}movement/downloadBulk`,
             type: 'POST',

           success: function(data) {
            var tbody='';
            data.list.forEach((item, index) => {
              tbody=tbody+'<tr><td>'+item.name+'</td><td>'+item.start_date+'</td><td>'+item.start_time+'</td><td>'+item.end_date+'</td><td>'+item.end_time+
                '<td>'+item.title+'</td><td>'+item.type+'</td><td>'+item.location+'</td><td>'+item.description+'</td><td>'+item.requested_at+'</td>'+
                '<td>'+(item.status == 0 ? '<span class="badge bg-secondary">Pending</span>' : (item.status == 1 ? '<span class="badge bg-success">Aproved</span>' : '<span class="badge bg-danger">Rejected</span>' ))+'</td>'+
                '<td>'+item.action_by_name+'</td><td>'+item.action_at+'</td>';
              });
              $('#MovementModal').modal('show');
            $(".datatables-leave-list #dataList").html(tbody);

           },
           error: function(data) {
               //
           }
       });

        }
    }
   else if(reportType ==3){
      if(view_type == 'excel' || view_type == 'pdf'){

        $.ajax({
             data:  {
              fromDate:fromDate,
                toDate:toDate,
                type:'2',
                view_type:view_type,
                employeeList:employeeList,
                "_token": "{{ csrf_token() }}",
            },
              url: `${baseUrl}leave/downloadBulk`,
              type: 'POST',
              xhrFields:{
                responseType: 'blob'
            },
            beforeSend: function() {
                //
            },
            success: function(data) {
                var url = window.URL || window.webkitURL;
                var objectUrl = url.createObjectURL(data);
                window.open(objectUrl);
            },
            error: function(data) {
                //
            }
        });
        }
        else{



          $.ajax({
            data:  {
             fromDate:fromDate,
               toDate:toDate,
               type:'2',
               view_type:view_type,
               employeeList:employeeList,
               "_token": "{{ csrf_token() }}",
           },
             url: `${baseUrl}leave/downloadBulk`,
             type: 'POST',

           success: function(data) {
            var tbody='';
            var tbody_sub='';
            data.list.forEach((item, index) => {
              {{--  tbody=tbody+'<tr><td>'+item.name+'</td><td>'+item.from+'</td><td>'+item.to+'</td><td>'+item.duration+'</td><td>'+item.requested_at+'</td><td>'+item.status+'</td>';  --}}



                  tbody_sub=tbody_sub+'<tr><td>'+item.name+'</td><td>'+item.leave_type+'</td><td>'+item.date+'</td><td>'+(item.leave_day_type == 1 ? 'Full Day' : item.leave_day_type == 2 ? 'FN' : 'AN')+'</td>'+
                    '<td>'+item.requested_at+'</td><td>'+(item.status == 0 ? '<span class="text-nowrap badge bg-label-secondary">Pending</span></td>' : (item.status == 1 ? '<span class="badge bg-label-success">Approved</span><br>Remark : '+item.remark+' ': '<span class="badge bg-label-danger">Rejected</span> <br>Remark : '+item.remark+ '</td>'))+'<td>'+item.action_by_name+'</td><td>'+item.action_at+'</td></tr>';



              });
              $('#LeaveModal').modal('show');
            $(".datatables-leave-list #dataList").html(tbody_sub);

           },
           error: function(data) {
               //
           }
       });

        }
    }
    else if(reportType ==4){
      if(view_type == 'excel' || view_type == 'pdf'){

        $.ajax({
             data:  {
              fromDate:fromDate,
                toDate:toDate,
                type:'2',
                view_type:view_type,
                employeeList:employeeList,
                "_token": "{{ csrf_token() }}",
            },
              url: `${baseUrl}misspunch/downloadBulk`,
              type: 'POST',
              xhrFields:{
                responseType: 'blob'
            },
            beforeSend: function() {
                //
            },
            success: function(data) {
                var url = window.URL || window.webkitURL;
                var objectUrl = url.createObjectURL(data);
                window.open(objectUrl);
            },
            error: function(data) {
                //
            }
        });
        }
        else{



          $.ajax({
            data:  {
             fromDate:fromDate,
               toDate:toDate,
               type:'2',
               view_type:view_type,
               employeeList:employeeList,
               "_token": "{{ csrf_token() }}",
           },
             url: `${baseUrl}misspunch/downloadBulk`,
             type: 'POST',

           success: function(data) {
            var tbody='';
            data.list.forEach((item, index) => {
              tbody=tbody+'<tr><td>'+item.name+'</td><td>'+item.date+'</td><td>'+(item.type == 1 ? "Checkin" : item.type == 2 ? "Checkout" : "Checkin&Checkout")+'</td><td>'+item.checkinTime+'</td><td>'+item.checkoutTime+
                '<td>'+item.description+'</td><td>'+item.requested_at+'</td>'+
                '<td>'+(item.status == 0 ? '<span class="badge bg-secondary">Pending</span>' : (item.status == 1 ? '<span class="badge bg-success">Aproved</span>' : '<span class="badge bg-danger">Rejected</span>' ))+'</td>'+
                '<td>'+item.action_by_name+'</td><td>'+item.action_at+'</td>';
              });
              $('#MisspunchModal').modal('show');
            $(".datatables-leave-list #dataList").html(tbody);

           },
           error: function(data) {
               //
           }
       });

        }
    }





});











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
  <div id="loading-overlay">
    <div class="loading-icon"></div>
</div>
  <div class="col">
    @can('attendance-management')
    <h6 class="mt-4">Import Attendance & Generate Report </h6>
    @endcan
    @can('team-attendance-management')
    <h6 class="mt-4"> Generate Report </h6>
    @endcan
    <div class="card mb-3">
      <div class="card-header">
        <ul class="nav nav-tabs card-header-tabs" role="tablist">
          <li class="nav-item  ">
            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#form-tabs-personal" role="tab" aria-selected="true">Generate Attendance / Leave / Movement Report</button>
          </li>
          @can('attendance-management')
          <li class="nav-item">
            <button class="nav-link " data-bs-toggle="tab" data-bs-target="#form-tabs-account" role="tab" aria-selected="false">Attendance Update</button>
          </li>
          @endcan

        </ul>
      </div>

      <div class="tab-content">
        <div class="tab-pane fade active show" id="form-tabs-personal" role="tabpanel">
          {{--  <form>  --}}
            <div class="row g-3">
              <div class="col-md-4 select2-primary">
                <label class="form-label" for="reportType">Report Type</label>
                <select id="reportType" class="select2 form-select" >
                  <option value="">Select All</option>

                  <option value='1' selected>Attendance</option>
                  <option value='2' >Movement</option>
                  <option value='3' >Leave</option>
                  <option value='4' >Miss punch</option>

                </select>
              </div>
              <div class="col-md-4">
                <label for="fromDate" class="form-label">From</label>
            <input type="text" class="form-control datepicker" id="fromDate" name="fromDate" placeholder="MM/DD/YYYY" class="form-control" />

              </div>
              <div class="col-md-4">
                <label for="toDate" class="form-label">To</label>
            <input type="text" class="form-control datepicker" id="toDate" name="toDate" placeholder="MM/DD/YYYY" class="form-control" />

              </div>
              {{--  <div class="col-md-4">
                <label class="form-label" for="formtabs-country">Employment Type</label>
                <select id="formtabs-country" class="select2 form-select" multiple data-allow-clear="true">
                  <option value="">Select All</option>
                  @foreach ($employment_types as $item)
                  <option value={{$item->id}}>{{$item->employment_type}}</option>
                  @endforeach


                </select>
              </div>  --}}
              <div class="col-md-4 select2-primary">
                <label class="form-label" for="employeeList">Employee</label>
                <select id="employeeList" class="select2 form-select" multiple>
                  <option value="">Select All</option>
                  @foreach ($employees as $item)
                  <option value={{$item->user_id}}>{{$item->name}}</option>
                  @endforeach
                </select>
              </div>

              <div class="col-md">
                <small class="text-light fw-medium d-block">View Type</small>
                <div class="form-check form-check-inline mt-3">
                  <input class="form-check-input" type="radio" checked name="viewTypeOptinon" id="viewTypeOptinon" value="html" />
                  <label class="form-check-label" for="inlineRadio1"><i class="ti ti-list ti-xs"></i> View</label>
                </div>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="viewTypeOptinon" id="viewTypeOptinon" value="pdf" />
                  <label class="form-check-label" for="inlineRadio2"><i class="ti ti-file-text"></i> PDF</label>
                </div>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="viewTypeOptinon" id="viewTypeOptinon" value="excel"  />
                  <label class="form-check-label" for="inlineRadio3"><i class="ti ti-file-spreadsheet ti-xs"></i> Excel</label>
                </div>
              </div>

            </div>
            <div class="pt-4">
              <button type="submit" id="report"   class="btn btn-primary me-sm-3 me-1">Generate</button>
              <button type="reset" class="btn btn-label-secondary">Cancel</button>
            </div>
          {{--  </form>  --}}
        </div>
        <div class="tab-pane fade" id="form-tabs-account" role="tabpanel">

            <div class="row">
              <!-- Basic  -->
              <div class="col-12">
                <div class="card mb-4">
                  <h5 class="card-header">Attendance Import</h5>
                  <div class="card-body">
                    {{--  <form action="upload"   enctype="multipart/form-data" class="dropzone needsclick" id="dropzone-basic">
                    @csrf
                      <div class="dz-message needsclick">
                        Drop files here or click to upload
                        <span class="note needsclick">(This is just a demo dropzone. Selected files are <span class="fw-medium">not</span> actually uploaded.)</span>
                      </div>
                      <div class="fallback">
                        <input name="file" id="file" type="file" />
                      </div>

                    </form>--}}
                      <form id="attendanceImport" enctype="multipart/form-data">
                    <input type="file" name="file"
                    class="form-control">
                      </form>
                  </div>
                </div>
              </div>
            <div class="pt-4">
              <button type="submit" id="import" class="btn btn-primary me-sm-3 me-1">Import</button>
              <button type="reset" class="btn btn-label-secondary">Cancel</button>

            </div>

        </div>

      </div>

    </div>
  </div>
</div>


<!-- Modal -->
@include('_partials/_modals/modal-attendance')
@include('_partials/_modals/modal-movement-report-view')
@include('_partials/_modals/modal-misspunch-report-view')
@include('_partials/_modals/modal-leave-report-view')
<!-- /Modal -->
@endsection
