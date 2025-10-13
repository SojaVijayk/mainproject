@extends('layouts/layoutMaster')

@section('title', 'Process Draft Invoice')

@section('content')
<div class="card">
  <div class="card-header">
    <h5 class="mb-0">Process Draft Invoice</h5>
  </div>
  <div class="card-body">
    <form action="{{ route('pms.finance.invoices.update', $invoice->id) }}" method="POST">
      @csrf
      @method('PUT')

      <div class="row mb-3">
        <div class="col-md-6">
          <label class="form-label">Project</label>
          <input type="text" class="form-control" value="{{ $invoice->project->title ?? 'N/A' }}" readonly>
        </div>
        <div class="col-md-6">
          <label class="form-label">Milestone</label>
          <input type="text" class="form-control" value="{{ $invoice->milestone?->name ?? 'N/A' }}" readonly>
        </div>
      </div>

      <div class="row mb-3">
        <div class="col-md-6">
          <label for="invoice_number" class="form-label">Invoice Number*</label>
          <input type="text" class="form-control" id="invoice_number" name="invoice_number"
            value="{{ old('invoice_number', $invoice->invoice_number) }}" required>
        </div>
        <div class="col-md-6">
          <label for="due_date" class="form-label">Due Date*</label>
          <input type="date" class="form-control" id="due_date" name="due_date"
            value="{{ old('due_date', $invoice->due_date?->format('Y-m-d')) }}" required>
        </div>
      </div>

      <div class="row mb-3">
        <div class="col-md-6">
          <label for="invoice_date" class="form-label">Invoice Date*</label>
          <input type="date" class="form-control" id="invoice_date" name="invoice_date"
            value="{{ old('invoice_date', $invoice->invoice_date?->format('Y-m-d')) }}" required>
        </div>
        <div class="col-md-6">
          <label for="amount" class="form-label">Amount*</label>
          <input type="number" step="0.01" min="0.01" class="form-control" id="amount" name="amount"
            value="{{ old('amount', $invoice->amount) }}" required>
        </div>
        <div class="col-md-6">
          <label for="tax_amount" class="form-label">GST Amount*</label>
          <input type="number" step="0.01" min="0.01" class="form-control" id="tax_amount" name="tax_amount"
            value="{{ old('amount', $invoice->tax_amount) }}" required>
        </div>
        <div class="col-md-6">
          <label for="total_amount" class="form-label">Total Amount*</label>
          <input type="number" step="0.01" min="0.01" class="form-control" id="total_amount" name="total_amount"
            value="{{ old('amount', $invoice->total_amount) }}" required>
        </div>
      </div>

      <div class="mb-3">
        <label for="description" class="form-label">Description</label>
        <textarea class="form-control" id="description" name="description"
          rows="3">{{ old('description', $invoice->description) }}</textarea>
      </div>

      <div class="d-flex justify-content-between">
        <a href="{{ route('pms.finance.invoices.index') }}" class="btn btn-secondary">Cancel</a>
        <button type="submit" class="btn btn-primary">Save Changes</button>
      </div>
    </form>
  </div>
</div>
@endsection