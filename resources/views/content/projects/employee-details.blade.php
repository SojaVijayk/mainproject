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


    // Service Status Toggle Logic
    $(document).on('change', '#edit_service_status_toggle', function() {
      var isChecked = $(this).is(':checked');
      var statusValue = isChecked ? 1 : 0;
      var statusLabel = isChecked ? 'Active' : 'Deactive';
      
      $('#edit_service_status').val(statusValue);
      $('#status_label').text(statusLabel);
      
      if (isChecked) {
        $('#end_date_container').slideUp();
      } else {
        $('#end_date_container').slideDown();
      }
    });


    // Pay Type Label Sync
    function updatePayLabel(payType) {
        var label = payType ? payType : 'Consolidated Pay';
        $('#pay_label').text(label);
        $('#edit_consolidated_pay').attr('placeholder', label);
    }

    $(document).on('change', '#edit_pay_type', function() {
        updatePayLabel($(this).val());
    });

    // Update main view label if changed (after sync, page reloads anyway, but good for consistency)
    $('#editServiceModal').on('shown.bs.modal', function() {
        updatePayLabel($('#edit_pay_type').val());
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
            <div class="user-info text-center">
              <h4 class="mb-2 mt-4">{{ $employee->name }} {{ $employee->last_name }}</h4>
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
              <span class="fw-semibold me-1">Project:</span>
              <span>{{ $employee->project->name ?? 'N/A' }}</span>
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
            <span class="fw-semibold d-block">Employment Type:</span>
            <span id="view_type">{{ $employee->service->employment_type ?? 'N/A' }}</span>
          </div>
          <div class="col-md-6 mb-3">
            <span class="fw-semibold d-block">Department:</span>
            <span id="view_department">{{ $employee->service->department ?? 'N/A' }}</span>
          </div>
          <div class="col-md-6 mb-3">
            <span class="fw-semibold d-block">Role:</span>
            <span id="view_role">{{ $employee->service->role ?? 'N/A' }}</span>
          </div>
          <div class="col-md-6 mb-3">
            <span class="fw-semibold d-block">Pay Type:</span>
            <span id="view_pay_type">{{ $employee->service->pay_type ?? 'N/A' }}</span>
          </div>
          <div class="col-md-6 mb-3">
            <span class="fw-semibold d-block" id="view_pay_label">{{ $employee->service->pay_type ?? 'Consolidated Pay' }}:</span>
            <span id="view_pay">{{ number_format($employee->service->consolidated_pay ?? 0, 2) }}</span>
          </div>
          <div class="col-md-6 mb-3">
            <span class="fw-semibold d-block">Status:</span>
            <span id="view_status" class="badge bg-label-{{ ($employee->service->status ?? 1) == 1 ? 'success' : 'danger' }}">
              {{ ($employee->service->status ?? 1) == 1 ? 'Active' : 'Deactive' }}
            </span>
          </div>
          <div class="col-md-6 mb-3">
            <span class="fw-semibold d-block">Start Date:</span>
            <span id="view_start">{{ $employee->service->start_date ?? 'N/A' }}</span>
          </div>
          @if(($employee->service->status ?? 1) == 0)
          <div class="col-md-6 mb-3" id="view_end_date_container">
            <span class="fw-semibold d-block">End Date:</span>
            <span id="view_end">{{ $employee->service->end_date ?? 'N/A' }}</span>
          </div>
          @endif
        </div>
      </div>
    </div>
    
    <!-- Service History -->
    <div class="card mb-4">
      <div class="card-header">
        <h5 class="mb-0">Service History</h5>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-sm table-hover">
            <thead>
              <tr>
                <th>Period</th>
                <th>Type</th>
                <th>Role</th>
                <th>Department</th>
                <th>Pay</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              @forelse($employee->services as $hist)
              <tr>
                <td>{{ $hist->start_date }} to {{ $hist->end_date ?? 'Present' }}</td>
                <td>{{ $hist->employment_type }}</td>
                <td>{{ $hist->role ?? 'N/A' }}</td>
                <td>{{ $hist->department }}</td>
                <td>{{ number_format($hist->consolidated_pay, 2) }} ({{ $hist->pay_type }})</td>
                <td>
                  <span class="badge bg-label-{{ $hist->status == 1 ? 'success' : 'secondary' }}">
                    {{ $hist->status == 1 ? 'Current' : 'Previous' }}
                  </span>
                </td>
              </tr>
              @empty
              <tr><td colspan="5" class="text-center">No service history found.</td></tr>
              @endforelse
            </tbody>
          </table>
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
            <label class="form-label" for="edit_employment_type">Employment Type</label>
            <select id="edit_employment_type" name="employment_type" class="form-select">
              <option value="Full Time" {{ ($employee->service->employment_type ?? '') == 'Full Time' ? 'selected' : '' }}>Full Time</option>
              <option value="Daily Wages" {{ ($employee->service->employment_type ?? '') == 'Daily Wages' ? 'selected' : '' }}>Daily Wages</option>
              <option value="Interns" {{ ($employee->service->employment_type ?? '') == 'Interns' ? 'selected' : '' }}>Interns</option>
              <option value="Contract" {{ ($employee->service->employment_type ?? '') == 'Contract' ? 'selected' : '' }}>Contract</option>
              <option value="Part Time" {{ ($employee->service->employment_type ?? '') == 'Part Time' ? 'selected' : '' }}>Part Time</option>
              <option value="Freelance" {{ ($employee->service->employment_type ?? '') == 'Freelance' ? 'selected' : '' }}>Freelance</option>
              <option value="Temporary" {{ ($employee->service->employment_type ?? '') == 'Temporary' ? 'selected' : '' }}>Temporary</option>
              <option value="Permanent" {{ ($employee->service->employment_type ?? '') == 'Permanent' ? 'selected' : '' }}>Permanent</option>
              <option value="Apprentice" {{ ($employee->service->employment_type ?? '') == 'Apprentice' ? 'selected' : '' }}>Apprentice</option>
            </select>
          </div>
          <div class="col-12 col-md-6">
            <label class="form-label" for="edit_department">Department</label>
            <input type="text" id="edit_department" name="department" class="form-control" value="{{ $employee->service->department ?? '' }}" placeholder="Engineering" />
          </div>
          <div class="col-12 col-md-6">
            <label class="form-label" for="edit_role">Role</label>
            <input type="text" id="edit_role" name="role" class="form-control" value="{{ $employee->service->role ?? '' }}" placeholder="System Administrator" />
          </div>
          <div class="col-12 col-md-6">
            <label class="form-label" for="edit_pay_type">Pay Type</label>
            <select id="edit_pay_type" name="pay_type" class="form-select">
              <option value="Hourly pay" {{ ($employee->service->pay_type ?? '') == 'Hourly pay' ? 'selected' : '' }}>Hourly pay</option>
              <option value="Daily wage" {{ ($employee->service->pay_type ?? '') == 'Daily wage' ? 'selected' : '' }}>Daily wage</option>
              <option value="Weekly pay" {{ ($employee->service->pay_type ?? '') == 'Weekly pay' ? 'selected' : '' }}>Weekly pay</option>
              <option value="Bi-weekly" {{ ($employee->service->pay_type ?? '') == 'Bi-weekly' ? 'selected' : '' }}>Bi-weekly</option>
              <option value="Monthly" {{ ($employee->service->pay_type ?? '') == 'Monthly' ? 'selected' : '' }}>Monthly</option>
              <option value="Annual" {{ ($employee->service->pay_type ?? '') == 'Annual' ? 'selected' : '' }}>Annual</option>
              <option value="Per diem" {{ ($employee->service->pay_type ?? '') == 'Per diem' ? 'selected' : '' }}>Per diem</option>
              <option value="Shift based pay" {{ ($employee->service->pay_type ?? '') == 'Shift based pay' ? 'selected' : '' }}>Shift based pay</option>
              <option value="Consolidated pay" {{ ($employee->service->pay_type ?? '') == 'Consolidated pay' ? 'selected' : '' }}>Consolidated pay</option>
            </select>
          </div>
          <div class="col-12 col-md-6">
            <label class="form-label" for="edit_consolidated_pay" id="pay_label">Consolidated Pay</label>
            <input type="number" step="0.01" id="edit_consolidated_pay" name="consolidated_pay" class="form-control" value="{{ $employee->service->consolidated_pay ?? '' }}" />
          </div>
          <div class="col-12 col-md-6">
            <label class="form-label d-block">Status</label>
            <div class="form-check form-switch mb-2">
              <input class="form-check-input" type="checkbox" id="edit_service_status_toggle" {{ ($employee->service->status ?? 1) == 1 ? 'checked' : '' }}>
              <label class="form-check-label" for="edit_service_status_toggle" id="status_label">{{ ($employee->service->status ?? 1) == 1 ? 'Active' : 'Deactive' }}</label>
            </div>
            <input type="hidden" name="status" id="edit_service_status" value="{{ $employee->service->status ?? 1 }}">
          </div>
          <div class="col-12 col-md-6">
            <label class="form-label" for="edit_start_date">Start Date</label>
            <input type="date" id="edit_start_date" name="start_date" class="form-control" value="{{ $employee->service->start_date ?? '' }}" />
          </div>
          <div class="col-12 col-md-6" id="end_date_container" style="{{ ($employee->service->status ?? 1) == 1 ? 'display: none;' : '' }}">
            <label class="form-label" for="edit_end_date">End Date</label>
            <input type="date" id="edit_end_date" name="end_date" class="form-control" value="{{ $employee->service->end_date ?? '' }}" />
          </div>
          <div class="col-12">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="new_record" value="1" id="check_new_record">
              <label class="form-check-label" for="check_new_record">
                Create as new service record? (Select this for promotions or role changes to preserve history)
              </label>
            </div>
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


@endsection

@push('page-script')
<script>
$(function() {
    $('.view-service-details').on('click', function() {
        Swal.fire({
            title: 'Service Details',
            html: '<div class="text-start"><strong>Project:</strong> {{ $employee->project->name ?? 'N/A' }}' +
                  '<br><strong>Department:</strong> {{ $employee->service->department ?? 'N/A' }}' +
                  '<br><strong>Role:</strong> {{ $employee->service->role ?? 'N/A' }}' +
                  '<br><strong>Type:</strong> {{ $employee->service->employment_type ?? 'N/A' }}' + 
                  '<br><strong>Pay Type:</strong> {{ $employee->service->pay_type ?? 'N/A' }}' + 
                  '<br><strong>Pay:</strong> {{ number_format($employee->service->consolidated_pay ?? 0, 2) }}' + 
                  '<br><strong>Status:</strong> {{ ($employee->service->status ?? 1) == 1 ? 'Active' : 'Deactive' }}' + 
                  '<br><strong>Start Date:</strong> {{ $employee->service->start_date ?? 'N/A' }}' + 
                  '{{ ($employee->service->status ?? 1) == 0 ? '<br><strong>End Date:</strong> ' . ($employee->service->end_date ?? 'N/A') : '' }}</div>',
            confirmButtonText: 'Close'
        });
    });

    $('#editServiceModal').on('show.bs.modal', function() {
        $('#check_new_record').prop('checked', false);
    });

});
</script>
@endpush
