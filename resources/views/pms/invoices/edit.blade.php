@extends('layouts/layoutMaster')

@section('title', 'Edit Invoices')

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
            dueDateInput.value = '';
        }
    });

    // Auto-calculate amount if milestone is selected
    const milestoneSelect = document.getElementById('milestone_id');
    const amountInput = document.getElementById('amount');

    milestoneSelect.addEventListener('change', function() {
        if (this.value) {
            const milestone = @json($milestones->keyBy('id'));
            const milestoneData = milestone[this.value];
            const amount = (milestoneData.weightage / 100) * {{ $project->budget }};
            amountInput.value = amount.toFixed(2);
        }
    });
});
</script>
@endsection
@section('header', 'Edit Invoice #' . $invoice->invoice_number)

@section('content')
<div class="row">
  <div class="col-md-8">
    <div class="card">
      <div class="card-body">
        <form action="{{ route('pms.invoices.update', [$project->id, $invoice->id]) }}" method="POST">
          @csrf
          @method('PUT')

          <div class="row mb-3">
            <div class="col-md-6">
              <label for="milestone_id" class="form-label">Milestone (Optional)</label>
              <select name="milestone_id" id="milestone_id" class="form-select">
                <option value="">Select Milestone</option>
                @foreach($milestones as $milestone)
                <option value="{{ $milestone->id }}" {{ $invoice->milestone_id == $milestone->id ? 'selected' : '' }}>
                  {{ $milestone->name }} ({{ $milestone->weightage }}%)
                </option>
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

                <option value="1" {{ $invoice->invoice_type == 'Proforma Invoice' ? 'selected' : ''
                  }}>Proforma Invoice</option>
                <option value="2" {{ $invoice->invoice_type == 'tax' ? 'selected' : '' }}>Tax Invoice</option>

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
                value="{{ old('invoice_date', $invoice->invoice_date->format('Y-m-d')) }}" required>
              @error('invoice_date')
              <div class="invalid-feedback d-block">{{ $message }}</div>
              @enderror
            </div>
            <div class="col-md-6">
              <label for="due_date" class="form-label">Due Date *</label>
              <input type="date" name="due_date" id="due_date" class="form-control"
                value="{{ old('due_date', $invoice->due_date->format('Y-m-d')) }}">
              @error('due_date')
              <div class="invalid-feedback d-block">{{ $message }}</div>
              @enderror
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-6">
              <label for="amount" class="form-label">Amount (₹ without tax)</label>
              <input type="number" step="0.01" min="0" name="amount" id="amount" class="form-control"
                value="{{ old('amount', $invoice->amount) }}" required>
              @error('amount')
              <div class="invalid-feedback d-block">{{ $message }}</div>
              @enderror
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-12">
              <label for="description" class="form-label">Description</label>
              <textarea name="description" id="description" class="form-control"
                rows="3">{{ old('description', $invoice->description) }}</textarea>
              @error('description')
              <div class="invalid-feedback d-block">{{ $message }}</div>
              @enderror
            </div>
          </div>

          <div class="mt-4">
            <button type="submit" class="btn btn-primary me-2">Update Invoice</button>
            <a href="{{ route('pms.invoices.show', [$project->id, $invoice->id]) }}"
              class="btn btn-secondary">Cancel</a>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="col-md-4">
    <div class="card">
      <div class="card-header">
        <h5 class="card-title">Invoice Status</h5>
      </div>
      <div class="card-body">
        <div class="alert alert-{{ $invoice->status_badge_color }}">
          <strong>Status:</strong> {{ $invoice->status_name }}
        </div>

        @if($invoice->invoice_number)
        <p><strong>Invoice #:</strong> {{ $invoice->invoice_number }}</p>
        @endif

        <p><strong>Created At:</strong> {{ $invoice->created_at->format('d M Y H:i') }}</p>
        <p><strong>Updated At:</strong> {{ $invoice->updated_at->format('d M Y H:i') }}</p>
      </div>
    </div>

    <div class="card mt-4">
      <div class="card-header">
        <h5 class="card-title">Project Details</h5>
      </div>
      <div class="card-body">
        <p><strong>Project Code:</strong> {{ $project->project_code }}</p>
        <p><strong>Title:</strong> {{ $project->title }}</p>
        <p><strong>Client:</strong> {{ $project->requirement->client->client_name }}</p>
        <p><strong>Budget:</strong> ₹{{ number_format($project->budget, 2) }}</p>
      </div>
    </div>
  </div>
</div>
@endsection