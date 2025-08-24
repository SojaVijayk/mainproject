@extends('layouts/layoutMaster')

@section('title', 'Create Proposals')

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
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const startDateInput = document.getElementById('expected_start_date');
    const endDateInput = document.getElementById('expected_end_date');

    const tenureYears = document.getElementById('tenure_years');
    const tenureMonths = document.getElementById('tenure_months');
    const tenureDays = document.getElementById('tenure_days');

    const budgetInput = document.getElementById('budget');
    const expenseInput = document.getElementById('estimated_expense');
    const revenueInput = document.getElementById('revenue');

    // --- Function 1: Calculate End Date based on Tenure ---
    function calculateEndDate() {
      if (startDateInput.value) {
        const startDate = new Date(startDateInput.value);
        const years = parseInt(tenureYears.value) || 0;
        const months = parseInt(tenureMonths.value) || 0;
        const days = parseInt(tenureDays.value) || 0;

        const endDate = new Date(startDate);
        endDate.setFullYear(endDate.getFullYear() + years);
        endDate.setMonth(endDate.getMonth() + months);
        endDate.setDate(endDate.getDate() + days);

        endDateInput.value = endDate.toISOString().split('T')[0];
      }
    }

    tenureYears.addEventListener('change', calculateEndDate);
    tenureMonths.addEventListener('change', calculateEndDate);
    tenureDays.addEventListener('change', calculateEndDate);

    // --- Function 2: Calculate Tenure based on Start & End Dates ---
    function calculateTenure() {
      if (startDateInput.value && endDateInput.value) {
        const startDate = new Date(startDateInput.value);
        const endDate = new Date(endDateInput.value);

        if (endDate < startDate) {
          tenureYears.value = 0;
          tenureMonths.value = 0;
          tenureDays.value = 0;
          return;
        }

        let years = endDate.getFullYear() - startDate.getFullYear();
        let months = endDate.getMonth() - startDate.getMonth();
        let days = endDate.getDate() - startDate.getDate();

        if (days < 0) {
          months -= 1;
          const prevMonth = new Date(endDate.getFullYear(), endDate.getMonth(), 0).getDate();
          days += prevMonth;
        }

        if (months < 0) {
          years -= 1;
          months += 12;
        }

        tenureYears.value = years;
        tenureMonths.value = months;
        tenureDays.value = days;
      }
    }

    startDateInput.addEventListener('change', function() {
      if (endDateInput.value) calculateTenure();
    });

    endDateInput.addEventListener('change', calculateTenure);

    function calculateRevenue() {
      const budget = parseFloat(budgetInput.value) || 0;
      const expense = parseFloat(expenseInput.value) || 0;
      const revenue = budget - expense;
      revenueInput.value = revenue >= 0 ? revenue.toFixed(2) : 0;
    }

    budgetInput.addEventListener('input', calculateRevenue);
    expenseInput.addEventListener('input', calculateRevenue);


  });
</script>
@endsection


@section('content')
<div class="row">
  <div class="col-md-8">
    <div class="card">
      <div class="card-header">
        <h5 class="card-title">Project:{{ $requirement->project_title }}- ( {{ $requirement->temp_no }} )</h5>
      </div>
      <div class="card-body">
        <form action="{{ route('pms.proposals.store', $requirement->id) }}" method="POST" enctype="multipart/form-data">
          @csrf

          <div class="row mb-3">
            <div class="col-md-6">
              <label for="budget" class="form-label">Budget (₹)</label>
              <input type="number" step="0.01" min="0" name="budget" id="budget" class="form-control"
                value="{{ old('budget') }}" required>
              @error('budget')
              <div class="invalid-feedback d-block">{{ $message }}</div>
              @enderror
            </div>
            <div class="col-md-6">
              <label for="expected_start_date" class="form-label">Expected Start Date</label>
              <input type="date" name="expected_start_date" id="expected_start_date" class="form-control"
                value="{{ old('expected_start_date') }}" min="{{ date('Y-m-d') }}" required>

              @error('expected_start_date')
              <div class="invalid-feedback d-block">{{ $message }}</div>
              @enderror
            </div>

          </div>

          <div class="row mb-3">


            <div class="col-md-6">
              <label for="expected_end_date" class="form-label">Expected End Date</label>
              <input type="date" name="expected_end_date" id="expected_end_date" class="form-control"
                value="{{ old('expected_end_date') }}" required>
              @error('expected_end_date')
              <div class="invalid-feedback d-block">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-md-6">
              <label class="form-label">Tenure</label>
              <div class="row g-2">
                <div class="col-4">
                  <input type="number" name="tenure_years" id="tenure_years" class="form-control" placeholder="Years"
                    value="{{ old('tenure_years', 0) }}" min="0">
                </div>
                <div class="col-4">
                  <input type="number" name="tenure_months" id="tenure_months" class="form-control" placeholder="Months"
                    value="{{ old('tenure_months', 0) }}" min="0" max="11">
                </div>
                <div class="col-4">
                  <input type="number" name="tenure_days" id="tenure_days" class="form-control" placeholder="Days"
                    value="{{ old('tenure_days', 0) }}" min="0" max="30">
                </div>
              </div>
              @error('tenure_years')
              <div class="invalid-feedback d-block">{{ $message }}</div>
              @enderror
              @error('tenure_months')
              <div class="invalid-feedback d-block">{{ $message }}</div>
              @enderror
              @error('tenure_days')
              <div class="invalid-feedback d-block">{{ $message }}</div>
              @enderror
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-6">
              <label for="estimated_expense" class="form-label">Estimated Expense (₹)</label>
              <input type="number" step="0.01" min="0" name="estimated_expense" id="estimated_expense"
                class="form-control" value="{{ old('estimated_expense') }}" required>
              @error('estimated_expense')
              <div class="invalid-feedback d-block">{{ $message }}</div>
              @enderror
            </div>
            <div class="col-md-6">
              <label for="revenue" class="form-label">Expected Revenue (₹)</label>
              <input type="number" step="0.01" min="0" name="revenue" id="revenue" class="form-control"
                value="{{ old('revenue') }}" required>
              @error('revenue')
              <div class="invalid-feedback d-block">{{ $message }}</div>
              @enderror
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-12">
              <label for="technical_details" class="form-label">Technical Details</label>
              <textarea name="technical_details" id="technical_details" class="form-control"
                rows="3">{{ old('technical_details') }}</textarea>
              @error('technical_details')
              <div class="invalid-feedback d-block">{{ $message }}</div>
              @enderror
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-12">
              <label for="methodology" class="form-label">Methodology</label>
              <textarea name="methodology" id="methodology" class="form-control"
                rows="3">{{ old('methodology') }}</textarea>
              @error('methodology')
              <div class="invalid-feedback d-block">{{ $message }}</div>
              @enderror
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-12">
              <label for="documents" class="form-label">Supporting Documents (Optional)</label>
              <input type="file" name="documents[]" id="documents" class="form-control" multiple>
              @error('documents')
              <div class="invalid-feedback d-block">{{ $message }}</div>
              @enderror
              <small class="text-muted">You can upload multiple files (Max 10MB each)</small>
            </div>
          </div>

          <div class="mt-4">
            <button type="submit" class="btn btn-primary me-2">Create Proposal</button>
            <a href="{{ route('pms.requirements.show', $requirement->id) }}" class="btn btn-secondary">Cancel</a>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="col-md-4">
    <div class="card">
      <div class="card-header">
        <h5 class="card-title">Requirement Details</h5>
      </div>
      <div class="card-body">
        {{-- <p><strong>Title:</strong> {{ $requirement->requirement->title }}</p> --}}
        <p><strong>Client:</strong> {{ $requirement->client->client_name }}</p>
        <p><strong>Category:</strong> {{ $requirement->category->name }}</p>
        <p><strong>Subcategory:</strong> {{ $requirement->subcategory->name ?? 'N/A' }}</p>
        <p><strong>Contact Person:</strong> {{ $requirement->contactPerson->name }}</p>
      </div>
    </div>
  </div>
</div>
@endsection