@extends('layouts/layoutMaster')

@section('title', 'Employee Details')

@section('vendor-style')
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}">
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}">
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
@endsection

@section('vendor-script')
<script src="{{ asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
@endsection

@section('page-script')
<script>
  $(function() {
    // Select2
    var select2 = $('.select2');
    if (select2.length) {
      select2.each(function() {
        var $this = $(this);
        $this.bootstrap5({
          dropdownParent: $this.parent()
        });
      });
    }

    // Submit form via AJAX
    $(document).on('click', '.btn-submit-edit', function() {
      var formId = $(this).data('form');
      var form = $('#' + formId);
      var url = form.attr('action');
      var formData = form.serialize();

      $.ajax({
        url: url,
        type: 'POST',
        data: formData,
        success: function(response) {
          if (response.success) {
            Swal.fire({
              icon: 'success',
              title: 'Success!',
              text: response.message,
              customClass: {
                confirmButton: 'btn btn-success'
              }
            }).then(() => {
              location.reload();
            });
          } else {
            Swal.fire({
              icon: 'error',
              title: 'Error!',
              text: response.message || 'Something went wrong',
              customClass: {
                confirmButton: 'btn btn-danger'
              }
            });
          }
        },
        error: function(xhr) {
          var errors = xhr.responseJSON.errors;
          var errorMsg = '';
          $.each(errors, function(key, value) {
            errorMsg += value[0] + '\n';
          });
          Swal.fire({
            icon: 'error',
            title: 'Validation Error!',
            text: errorMsg,
            customClass: {
              confirmButton: 'btn btn-danger'
            }
          });
        }
      });
    });
    $('.view-service-details').on('click', function() {
        Swal.fire({
            title: 'Service Details',
            html: '<div class="text-start"><strong>Department:</strong> ' + $('#view_dept').text() + 
                  '<br><strong>Type:</strong> ' + $('#view_type').text() + 
                  '<br><strong>Start Date:</strong> ' + $('#view_start').text() + '</div>',
            confirmButtonText: 'Close'
        });
    });

    $('.view-salary-details').on('click', function() {
        Swal.fire({
            title: 'Salary Details',
            html: '<div class="text-start"><strong>Basic Pay:</strong> ' + $('#view_basic').text() + 
                  '<br><strong>HRA:</strong> ' + $('#view_hra').text() + 
                  '<br><strong>Other:</strong> ' + $('#view_allow').text() + 
                  '<br><strong>Gross:</strong> ' + $('#view_gross').text() + '</div>',
            confirmButtonText: 'Close'
        });
    });

    $('.view-deduction-details').on('click', function() {
        Swal.fire({
            title: 'Deduction Details',
            html: '<div class="text-start"><strong>PF:</strong> ' + $('#view_pf').text() + 
                  '<br><strong>ESI:</strong> ' + $('#view_esi').text() + 
                  '<br><strong>Prof. Tax:</strong> ' + $('#view_pt').text() + 
                  '<br><strong>Total:</strong> ' + $('#view_total_ded').text() + '</div>',
            confirmButtonText: 'Close'
        });
    });

  });
</script>
@endsection

@section('content')
<h4 class="fw-bold py-3 mb-4">
  <span class="text-muted fw-light">Project Management / Employees /</span> Details
</h4>

<div class="row">
  <!-- Employee Master Details (Left Sidebar) -->
  <div class="col-xl-4 col-lg-5 col-md-5 order-1 order-md-0">
    <div class="card mb-4">
      <div class="card-body">
        <div class="user-avatar-section">
          <div class="d-flex align-items-center flex-column">
            <img class="img-fluid rounded mb-3 pt-1 mt-4" src="{{ asset('assets/img/avatars/1.png') }}" height="100" width="100" alt="User avatar" />
            <div class="user-info text-center">
              <h4 class="mb-2">{{ $employee->name }} {{ $employee->last_name }}</h4>
              <span class="badge bg-label-secondary mt-1">{{ $employee->designation->designation ?? 'N/A' }}</span><br>
              <span class="badge bg-label-success mt-1">{{ $employee->empId }}</span>
            </div>
          </div>
        </div>
        <p class="mt-4 small text-uppercase text-muted">Master Details</p>
        <div class="info-container">
          <ul class="list-unstyled">
            <li class="mb-2">
              <span class="fw-semibold me-1">Email:</span>
              <span>{{ $employee->email }}</span>
            </li>
            <li class="mb-2 pt-1">
              <span class="fw-semibold me-1">Mobile:</span>
              <span>{{ $employee->mobile }}</span>
            </li>
            <li class="mb-2 pt-1">
              <span class="fw-semibold me-1">Age:</span>
              <span>{{ $employee->age }}</span>
            </li>
            <li class="mb-2 pt-1">
              <span class="fw-semibold me-1">DOB:</span>
              <span>{{ $employee->dob }}</span>
            </li>
            <li class="mb-2 pt-1">
              <span class="fw-semibold me-1">Joining Date:</span>
              <span>{{ $employee->date_of_joining }}</span>
            </li>
            <li class="mb-2 pt-1">
              <span class="fw-semibold me-1">Address:</span>
              <span>{{ $employee->address }}</span>
            </li>
            <li class="mb-2 pt-1">
                <span class="fw-semibold me-1">Status:</span>
                <span class="badge bg-label-success">{{ $employee->status == 1 ? 'Active' : 'Inactive' }}</span>
              </li>
          </ul>
          <div class="d-flex justify-content-center">
            <button class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#editMasterModal">Edit Master Info</button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Service, Salary, Deduction Details (Right Content) -->
  <div class="col-xl-8 col-lg-7 col-md-7 order-0 order-md-1">
    <!-- Service Details -->
    <div class="card mb-4">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Service Details</h5>
        <div class="dropdown">
          <button class="btn p-0" type="button" id="serviceOptions" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="ti ti-dots-vertical ti-sm"></i>
          </button>
          <div class="dropdown-menu dropdown-menu-end" aria-labelledby="serviceOptions">
            <a class="dropdown-item" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#editServiceModal">Edit Service Info</a>
            <a class="dropdown-item view-service-details" href="javascript:void(0);">View Details</a>
          </div>
        </div>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-6 mb-3">
            <span class="fw-semibold d-block">Department:</span>
            <span id="view_dept">{{ $employee->service->department ?? 'N/A' }}</span>
          </div>
          <div class="col-md-6 mb-3">
            <span class="fw-semibold d-block">Employment Type:</span>
            <span id="view_type">{{ $employee->service->employment_type ?? 'N/A' }}</span>
          </div>
          <div class="col-md-6 mb-3">
            <span class="fw-semibold d-block">Start Date:</span>
            <span id="view_start">{{ $employee->service->start_date ?? 'N/A' }}</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Salary Details -->
    <div class="card mb-4">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Salary Details</h5>
        <div class="dropdown">
          <button class="btn p-0" type="button" id="salaryOptions" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="ti ti-dots-vertical ti-sm"></i>
          </button>
          <div class="dropdown-menu dropdown-menu-end" aria-labelledby="salaryOptions">
            <a class="dropdown-item" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#editSalaryModal">Edit Salary Info</a>
            <a class="dropdown-item view-salary-details" href="javascript:void(0);">View Details</a>
          </div>
        </div>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-4 mb-3">
            <span class="fw-semibold d-block">Basic Pay:</span>
            <span id="view_basic">{{ number_format($employee->salary->basic_pay ?? 0, 2) }}</span>
          </div>
          <div class="col-md-4 mb-3">
            <span class="fw-semibold d-block">HRA:</span>
            <span id="view_hra">{{ number_format($employee->salary->hra ?? 0, 2) }}</span>
          </div>
          <div class="col-md-4 mb-3">
            <span class="fw-semibold d-block">Other Allowance:</span>
            <span id="view_allow">{{ number_format($employee->salary->other_allowance ?? 0, 2) }}</span>
          </div>
          <div class="col-md-4 mb-3">
            <span class="fw-semibold d-block">Gross Salary:</span>
            <span class="text-success fw-bold" id="view_gross">{{ number_format($employee->salary->gross_salary ?? 0, 2) }}</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Deduction Details -->
    <div class="card mb-4">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Deduction Details</h5>
        <div class="dropdown">
          <button class="btn p-0" type="button" id="deductionOptions" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="ti ti-dots-vertical ti-sm"></i>
          </button>
          <div class="dropdown-menu dropdown-menu-end" aria-labelledby="deductionOptions">
            <a class="dropdown-item" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#editDeductionModal">Edit Deduction Info</a>
            <a class="dropdown-item view-deduction-details" href="javascript:void(0);">View Details</a>
          </div>
        </div>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-4 mb-3">
            <span class="fw-semibold d-block">PF:</span>
            <span id="view_pf">{{ number_format($employee->deduction->pf ?? 0, 2) }}</span>
          </div>
          <div class="col-md-4 mb-3">
            <span class="fw-semibold d-block">ESI:</span>
            <span id="view_esi">{{ number_format($employee->deduction->esi ?? 0, 2) }}</span>
          </div>
          <div class="col-md-4 mb-3">
            <span class="fw-semibold d-block">Professional Tax:</span>
            <span id="view_pt">{{ number_format($employee->deduction->professional_tax ?? 0, 2) }}</span>
          </div>
          <div class="col-md-4 mb-3">
            <span class="fw-semibold d-block">Total Deductions:</span>
            <span class="text-danger fw-bold" id="view_total_ded">{{ number_format($employee->deduction->total_deductions ?? 0, 2) }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modals -->
<!-- ... (Existing Modals) ... -->




<!-- Modals -->

<!-- Edit Master Modal -->
<div class="modal fade" id="editMasterModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-simple modal-edit-user">
    <div class="modal-content p-3 p-md-5">
      <div class="modal-body">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="text-center mb-4">
          <h3 class="mb-2">Edit Master Information</h3>
          <p class="text-muted">Update employee primary details.</p>
        </div>
        <form id="editMasterForm" class="row g-3" action="{{ route('pms.employees.update-master', $employee->id) }}" onsubmit="return false">
          @csrf
          <div class="col-12 col-md-6">
            <label class="form-label" for="edit_name">First Name</label>
            <input type="text" id="edit_name" name="name" class="form-control" value="{{ $employee->name }}" />
          </div>
          <div class="col-12 col-md-6">
            <label class="form-label" for="edit_last_name">Last Name</label>
            <input type="text" id="edit_last_name" name="last_name" class="form-control" value="{{ $employee->last_name }}" />
          </div>
          <div class="col-12 col-md-6">
            <label class="form-label" for="edit_email">Email</label>
            <input type="text" id="edit_email" name="email" class="form-control" value="{{ $employee->email }}" />
          </div>
          <div class="col-12 col-md-6">
            <label class="form-label" for="edit_mobile">Mobile</label>
            <input type="text" id="edit_mobile" name="mobile" class="form-control" value="{{ $employee->mobile }}" />
          </div>
          <div class="col-12 col-md-4">
            <label class="form-label" for="edit_age">Age</label>
            <input type="number" id="edit_age" name="age" class="form-control" value="{{ $employee->age }}" />
          </div>
          <div class="col-12 col-md-4">
            <label class="form-label" for="edit_dob">DOB</label>
            <input type="date" id="edit_dob" name="dob" class="form-control" value="{{ $employee->dob }}" />
          </div>
          <div class="col-12 col-md-4">
            <label class="form-label" for="edit_joining_date">Joining Date</label>
            <input type="date" id="edit_joining_date" name="joining_date" class="form-control" value="{{ $employee->date_of_joining }}" />
          </div>
          <div class="col-12">
            <label class="form-label" for="edit_address">Address</label>
            <textarea id="edit_address" name="address" class="form-control" rows="2">{{ $employee->address }}</textarea>
          </div>
          <div class="col-12 text-center">
            <button type="submit" class="btn btn-primary me-sm-3 me-1 btn-submit-edit" data-form="editMasterForm">Submit</button>
            <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal" aria-label="Close">Cancel</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Edit Service Modal -->
<div class="modal fade" id="editServiceModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-simple">
    <div class="modal-content p-3 p-md-5">
      <div class="modal-body">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="text-center mb-4">
          <h3 class="mb-2">Edit Service Information</h3>
        </div>
        <form id="editServiceForm" class="row g-3" action="{{ route('pms.employees.update-service', $employee->p_id) }}" onsubmit="return false">
          @csrf
          <div class="col-12 col-md-6">
            <label class="form-label" for="edit_department">Department</label>
            <input type="text" id="edit_department" name="department" class="form-control" value="{{ $employee->service->department ?? '' }}" />
          </div>
          <div class="col-12 col-md-6">
            <label class="form-label" for="edit_employment_type">Employment Type</label>
            <select id="edit_employment_type" name="employment_type" class="form-select">
              <option value="Regular" {{ ($employee->service->employment_type ?? '') == 'Regular' ? 'selected' : '' }}>Regular</option>
              <option value="Contract" {{ ($employee->service->employment_type ?? '') == 'Contract' ? 'selected' : '' }}>Contract</option>
              <option value="Intern" {{ ($employee->service->employment_type ?? '') == 'Intern' ? 'selected' : '' }}>Intern</option>
            </select>
          </div>
          <div class="col-12 col-md-6">
            <label class="form-label" for="edit_start_date">Start Date</label>
            <input type="date" id="edit_start_date" name="start_date" class="form-control" value="{{ $employee->service->start_date ?? '' }}" />
          </div>
          <div class="col-12 text-center">
            <button type="submit" class="btn btn-primary me-sm-3 me-1 btn-submit-edit" data-form="editServiceForm">Submit</button>
            <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal" aria-label="Close">Cancel</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Edit Salary Modal -->
<div class="modal fade" id="editSalaryModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-simple">
    <div class="modal-content p-3 p-md-5">
      <div class="modal-body">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="text-center mb-4">
          <h3 class="mb-2">Edit Salary Information</h3>
        </div>
        <form id="editSalaryForm" class="row g-3" action="{{ route('pms.employees.update-salary', $employee->p_id) }}" onsubmit="return false">
          @csrf
          <div class="col-12 col-md-4">
            <label class="form-label" for="edit_basic_pay">Basic Pay</label>
            <input type="number" id="edit_basic_pay" name="basic_pay" class="form-control" value="{{ $employee->salary->basic_pay ?? 0 }}" />
          </div>
          <div class="col-12 col-md-4">
            <label class="form-label" for="edit_hra">HRA</label>
            <input type="number" id="edit_hra" name="hra" class="form-control" value="{{ $employee->salary->hra ?? 0 }}" />
          </div>
          <div class="col-12 col-md-4">
            <label class="form-label" for="edit_other_allowance">Other Allowance</label>
            <input type="number" id="edit_other_allowance" name="other_allowance" class="form-control" value="{{ $employee->salary->other_allowance ?? 0 }}" />
          </div>
          <div class="col-12 text-center">
            <button type="submit" class="btn btn-primary me-sm-3 me-1 btn-submit-edit" data-form="editSalaryForm">Submit</button>
            <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal" aria-label="Close">Cancel</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Edit Deduction Modal -->
<div class="modal fade" id="editDeductionModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-simple">
    <div class="modal-content p-3 p-md-5">
      <div class="modal-body">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="text-center mb-4">
          <h3 class="mb-2">Edit Deduction Information</h3>
        </div>
        <form id="editDeductionForm" class="row g-3" action="{{ route('pms.employees.update-deduction', $employee->p_id) }}" onsubmit="return false">
          @csrf
          <div class="col-12 col-md-4">
            <label class="form-label" for="edit_pf">PF</label>
            <input type="number" id="edit_pf" name="pf" class="form-control" value="{{ $employee->deduction->pf ?? 0 }}" />
          </div>
          <div class="col-12 col-md-4">
            <label class="form-label" for="edit_esi">ESI</label>
            <input type="number" id="edit_esi" name="esi" class="form-control" value="{{ $employee->deduction->esi ?? 0 }}" />
          </div>
          <div class="col-12 col-md-4">
            <label class="form-label" for="edit_professional_tax">Professional Tax</label>
            <input type="number" id="edit_professional_tax" name="professional_tax" class="form-control" value="{{ $employee->deduction->professional_tax ?? 0 }}" />
          </div>
          <div class="col-12 text-center">
            <button type="submit" class="btn btn-primary me-sm-3 me-1 btn-submit-edit" data-form="editDeductionForm">Submit</button>
            <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal" aria-label="Close">Cancel</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

@endsection

@push('page-script')
<script>
$(function() {
    $('.view-service-details').on('click', function() {
        Swal.fire({
            title: 'Service Details',
            html: '<div class="text-start"><strong>Department:</strong> ' + $('#view_dept').text() + 
                  '<br><strong>Type:</strong> ' + $('#view_type').text() + 
                  '<br><strong>Start Date:</strong> ' + $('#view_start').text() + '</div>',
            confirmButtonText: 'Close'
        });
    });

    $('.view-salary-details').on('click', function() {
        Swal.fire({
            title: 'Salary Details',
            html: '<div class="text-start"><strong>Basic Pay:</strong> ' + $('#view_basic').text() + 
                  '<br><strong>HRA:</strong> ' + $('#view_hra').text() + 
                  '<br><strong>Other:</strong> ' + $('#view_allow').text() + 
                  '<br><strong>Gross:</strong> ' + $('#view_gross').text() + '</div>',
            confirmButtonText: 'Close'
        });
    });

    $('.view-deduction-details').on('click', function() {
        Swal.fire({
            title: 'Deduction Details',
            html: '<div class="text-start"><strong>PF:</strong> ' + $('#view_pf').text() + 
                  '<br><strong>ESI:</strong> ' + $('#view_esi').text() + 
                  '<br><strong>Prof. Tax:</strong> ' + $('#view_pt').text() + 
                  '<br><strong>Total:</strong> ' + $('#view_total_ded').text() + '</div>',
            confirmButtonText: 'Close'
        });
    });
});
</script>
@endpush
