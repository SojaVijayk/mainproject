@extends('layouts/layoutMaster')

@section('title', 'Salary Management - Calculation')

@section('content')
<h4 class="fw-bold py-3 mb-4">
  <span class="text-muted fw-light">PMS / Salary Management /</span> Step 3: Calculation & Processing
</h4>

<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <div>
      <h5 class="mb-0">Payroll Calculation for {{ $month }} {{ $year }}</h5>
      <span class="badge bg-label-info">{{ ucfirst($employmentType) }}</span>
    </div>
  </div>
  <div class="card-body">
    <form action="{{ route('pms.salary-management.store', $project_id) }}" method="POST" id="payroll-form">
      @csrf
      <input type="hidden" name="month" value="{{ $month }}">
      <input type="hidden" name="year" value="{{ $year }}">
      <input type="hidden" name="employment_type" value="{{ $employmentType }}">
      <input type="hidden" name="freeze" id="freeze-input" value="0">

      <div class="table-responsive text-nowrap mb-4">
        <table class="table table-bordered table-sm">
          <thead>
            <tr class="text-center">
              <th>Employee Name</th>
              <th>Designation</th>
              <th style="width: 120px;">Monthly Working Days</th>
              <th style="width: 120px;">Days Worked</th>
              <th>Base Salary</th>
              <th>Total Calculated Salary</th>
            </tr>
          </thead>
          <tbody>
            @foreach($employees as $employee)
            <tr>
              <td>
                {{ $employee->name }}
                <input type="hidden" name="p_id[]" value="{{ $employee->p_id }}">
              </td>
              <td>{{ $employee->designation ?? $employee->role ?? 'N/A' }}</td>
              <td>
                <input type="number" name="monthly_working_days[]" class="form-control form-control-sm text-center working-days" value="30" min="1" required>
              </td>
              <td>
                <input type="number" name="days_worked[]" class="form-control form-control-sm text-center days-worked" value="30" min="0" required>
              </td>
              <td>
                <input type="number" name="base_salary[]" class="form-control form-control-sm text-end base-salary" value="{{ $employee->consolidated_pay }}" readonly>
              </td>
              <td>
                <input type="number" step="0.01" name="total_salary[]" class="form-control form-control-sm text-end total-salary" value="{{ $employee->consolidated_pay }}" readonly>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      <div class="d-flex justify-content-between align-items-center">
        <a href="{{ url()->previous() }}" class="btn btn-label-secondary">Back</a>
        <div class="d-flex gap-2">
          <button type="button" class="btn btn-primary" onclick="submitForm('{{ route('pms.salary-management.store', $project_id) }}', '1')">
            <i class="ti ti-lock me-1"></i> Freeze (Save)
          </button>
          <button type="button" class="btn btn-success" onclick="submitForm('{{ route('pms.salary-management.summary', $project_id) }}', '0')">
            <i class="ti ti-arrow-right me-1"></i> Process (Next)
          </button>
        </div>
      </div>
    </form>
  </div>
</div>

<script>
function submitForm(action, freezeValue) {
  const form = document.getElementById('payroll-form');
  document.getElementById('freeze-input').value = freezeValue;
  form.action = action;
  form.submit();
}

document.querySelectorAll('.working-days, .days-worked').forEach(input => {
  input.addEventListener('change', function() {
    const row = this.closest('tr');
    const workingDays = parseFloat(row.querySelector('.working-days').value) || 0;
    const daysWorked = parseFloat(row.querySelector('.days-worked').value) || 0;
    const baseSalary = parseFloat(row.querySelector('.base-salary').value) || 0;
    
    if (workingDays > 0) {
      const totalSalary = (baseSalary / workingDays) * daysWorked;
      row.querySelector('.total-salary').value = totalSalary.toFixed(2);
    }
  });
});
</script>

@push('page-script')
<script>
$(function() {
    function calculate() {
        $('tbody tr').each(function() {
            var row = $(this);
            var workingDays = parseFloat(row.find('.working-days').val()) || 0;
            var daysWorked = parseFloat(row.find('.days-worked').val()) || 0;
            var baseSalary = parseFloat(row.find('.base-salary').val()) || 0;
            
            if (workingDays > 0) {
                var total = (baseSalary / workingDays) * daysWorked;
                row.find('.total-salary').val(total.toFixed(2));
            } else {
                row.find('.total-salary').val(0.00);
            }
        });
    }

    $(document).on('input', '.working-days, .days-worked', function() {
        calculate();
    });

    // Initial calculation
    calculate();
});
</script>
@endpush
@endsection
