@extends('layouts/layoutMaster')

@section('title', 'Create Invoices')

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
    // Set due date minimum based on invoice date
    const invoiceDateInput = document.getElementById('invoice_date');
    const dueDateInput = document.getElementById('due_date');

    invoiceDateInput.addEventListener('change', function() {
        dueDateInput.min = this.value;

        // If due date is before invoice date, reset it
        if (dueDateInput.value && dueDateInput.value < this.value) {
            dueDateInput.value = this.value;
        }
    });
});
</script>
@endsection
@section('header', 'Create Invoice for ' . $project->title)

@section('content')
<div class="row">
  <div class="col-md-8">
    <div class="card">
      <div class="card-body">
        <form action="{{ route('pms.invoices.store', $project->id) }}" method="POST">
          @csrf

          <div class="row mb-3">
            <div class="col-md-6">
              <label for="milestone_id" class="form-label">Milestone (Optional)</label>
              <select name="milestone_id" id="milestone_id" class="form-select">
                <option value="">Select Milestone</option>
                @foreach($milestones as $milestone)
                <option value="{{ $milestone->id }}">{{ $milestone->name }}</option>
                @endforeach
              </select>
              @error('milestone_id')
              <div class="invalid-feedback d-block">{{ $message }}</div>
              @enderror
            </div>
            <div class="col-md-6">
              <label for="invoice_type" class="form-label">Invoice Type</label>
              <select name="invoice_type" id="invoice_type" class="form-select">
                <option value="">Select Invoice Type</option>

                <option value="1">Proforma Invoice</option>
                <option value="2">Tax Invoice</option>

              </select>
              @error('milestone_id')
              <div class="invalid-feedback d-block">{{ $message }}</div>
              @enderror
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-6">
              <label for="invoice_date" class="form-label">Invoice Date</label>
              <input type="date" name="invoice_date" id="invoice_date" class="form-control"
                value="{{ old('invoice_date', date('Y-m-d')) }}" required>
              @error('invoice_date')
              <div class="invalid-feedback d-block">{{ $message }}</div>
              @enderror
            </div>
            <div class="col-md-6">
              <label for="due_date" class="form-label">Due Date</label>
              <input type="date" name="due_date" id="due_date" class="form-control"
                value="{{ old('due_date', date('Y-m-d', strtotime('+30 days'))) }}">
              @error('due_date')
              <div class="invalid-feedback d-block">{{ $message }}</div>
              @enderror
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-6">
              <label for="amount" class="form-label">Amount (₹ without tax)</label>
              <input type="number" step="0.01" min="0" name="amount" id="amount" class="form-control"
                value="{{ old('amount') }}" required>
              @error('amount')
              <div class="invalid-feedback d-block">{{ $message }}</div>
              @enderror
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-12">
              <label for="description" class="form-label">Description</label>
              <textarea name="description" id="description" class="form-control"
                rows="3">{{ old('description') }}</textarea>
              @error('description')
              <div class="invalid-feedback d-block">{{ $message }}</div>
              @enderror
            </div>
          </div>

          <div class="mt-4">
            <button type="submit" class="btn btn-primary me-2">Create Invoice</button>
            <a href="{{ route('pms.projects.show', $project->id) }}" class="btn btn-secondary">Cancel</a>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="col-md-4">
    <div class="card">
      <div class="card-header">
        <h5 class="card-title">Project Financials</h5>
      </div>
      <div class="card-body">
        <p><strong>Budget:</strong> ₹{{ number_format($project->budget, 2) }}</p>
        <p><strong>Total Invoiced:</strong> ₹{{ number_format($project->invoices->sum('amount'), 2) }}</p>
        <p><strong>Total Paid:</strong> ₹{{ number_format($project->invoices->sum(function($i) { return
          $i->payments->sum('amount'); }), 2) }}</p>
        <p><strong>Pending Payment:</strong> ₹{{ number_format($project->invoices->sum('amount') -
          $project->invoices->sum(function($i) { return $i->payments->sum('amount'); }), 2) }}</p>
      </div>
    </div>
  </div>
</div>
@endsection