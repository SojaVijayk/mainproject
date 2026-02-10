@extends('layouts/layoutMaster')

@section('title', 'Salary Management - Step 4: Review Summary')

@section('content')
<h4 class="fw-bold py-3 mb-4">
  <span class="text-muted fw-light">PMS / Salary Management /</span> Step 4: Review & Summary
</h4>

<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <div>
      <h5 class="card-title mb-0">Payroll Review for {{ $month }} {{ $year }}</h5>
      <small class="text-muted">Final checks before processing</small>
    </div>
  </div>
  <div class="card-body">
    <form action="{{ route('pms.salary-management.store', $project_id) }}" method="POST">
      @csrf
      <input type="hidden" name="month" value="{{ $month }}">
      <input type="hidden" name="year" value="{{ $year }}">
      <input type="hidden" name="employment_type" value="{{ $employmentType }}">
      <input type="hidden" name="freeze" value="1">

      <div class="table-responsive text-nowrap mb-4">
        <table class="table table-hover">
          <thead class="table-light">
            <tr>
              <th>Employee Name</th>
              <th class="text-center">Working Days</th>
              <th class="text-center">Days Worked</th>
              <th class="text-end">Base Salary</th>
              <th class="text-end">Net Payable</th>
            </tr>
          </thead>
          <tbody>
            @php $grandTotal = 0; @endphp
            @foreach($summaryData as $data)
            <tr>
              <td>
                <strong>{{ $data['name'] }}</strong>
                <input type="hidden" name="p_id[]" value="{{ $data['p_id'] }}">
                <input type="hidden" name="monthly_working_days[]" value="{{ $data['working_days'] }}">
                <input type="hidden" name="days_worked[]" value="{{ $data['days_worked'] }}">
                <input type="hidden" name="base_salary[]" value="{{ $data['base_salary'] }}">
                <input type="hidden" name="total_salary[]" value="{{ $data['total_salary'] }}">
              </td>
              <td class="text-center">{{ $data['working_days'] }}</td>
              <td class="text-center">{{ $data['days_worked'] }}</td>
              <td class="text-end">₹{{ number_format($data['base_salary'], 2) }}</td>
              <td class="text-end text-primary fw-bold">₹{{ number_format($data['total_salary'], 2) }}</td>
            </tr>
            @php $grandTotal += $data['total_salary']; @endphp
            @endforeach
          </tbody>
          <tfoot>
            <tr class="table-primary">
              <td colspan="4" class="text-end fw-bold">Grand Total:</td>
              <td class="text-end fw-bold">₹{{ number_format($grandTotal, 2) }}</td>
            </tr>
          </tfoot>
        </table>
      </div>

      <div class="alert alert-info d-flex" role="alert">
        <span class="badge badge-center rounded-pill bg-info border-label-info p-3 me-2">
          <i class="ti ti-info-circle ti-xs"></i>
        </span>
        <div class="d-flex flex-column ps-1">
          <h6 class="alert-heading d-flex align-items-center fw-bold mb-1">Final Process Confirmation</h6>
          <span>Processing this payroll will freeze the records and prepare them for payment. Please review all amounts carefully.</span>
        </div>
      </div>

      <div class="d-flex justify-content-between align-items-center">
        <a href="{{ url()->previous() }}" class="btn btn-label-secondary">Back to Calculation</a>
        <button type="submit" class="btn btn-success btn-lg">
          <i class="ti ti-check me-1"></i> Finalize & Process Payroll
        </button>
      </div>
    </form>
  </div>
</div>
@endsection
