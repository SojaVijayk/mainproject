@extends('layouts/layoutMaster')

@section('title', 'Invoices')

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

@endsection
@section('header', 'Invoices for ' . $project->title)

@section('content')
<div class="card">
  <div class="card-header">
    <div class="d-flex justify-content-between align-items-center">
      <h5 class="mb-0">Invoices List</h5>
      @if($project->status != \App\Models\PMS\Project::STATUS_ARCHIVED)
      <a href="{{ route('pms.invoices.create', $project->id) }}" class="btn btn-sm btn-primary">
        <i class="fas fa-plus"></i> Add Invoice
      </a>
      @endif
    </div>
  </div>
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-hover">
        <thead>
          <tr>
            <th>Invoice #</th>
            <th>Type #</th>
            <th>Date</th>
            <th>Due Date</th>
            <th>Amount</th>
            <th>Tax</th>
            <th>Total</th>
            <th>Paid</th>
            <th>Balance</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($invoices as $invoice)
          <tr>
            <td>{{ $invoice->invoice_number ?? 'Draft' }}</td>
            <td> <span class="badge bg-{{$invoice->invoice_type == 1 ? 'primary' : 'success'}}">
                {{ $invoice->invoice_type == 1 ? 'Proforma Invoice' : 'Tax Invoice' }}
              </span></td>
            <td>{{ $invoice->invoice_date->format('d M Y') }}</td>
            <td>{{ $invoice->due_date->format('d M Y') }}</td>
            <td>₹{{ number_format($invoice->amount, 2) }}</td>
            <td>₹{{ number_format($invoice->tax_amount, 2) }}</td>
            <td>₹{{ number_format($invoice->total_amount, 2) }}</td>
            <td>₹{{ number_format($invoice->paid_amount, 2) }}</td>
            <td>₹{{ number_format($invoice->balance_amount, 2) }}</td>
            <td>
              <span class="badge bg-{{ $invoice->status_badge_color }}">
                {{ $invoice->status_name }}
              </span>
            </td>
            <td>
              <a href="{{ route('pms.invoices.show', [$project->id, $invoice->id]) }}" class="btn btn-sm btn-info">
                <i class="fas fa-eye"></i>
              </a>
              @if($invoice->status == \App\Models\PMS\Invoice::STATUS_DRAFT)
              <a href="{{ route('pms.invoices.edit', [$project->id, $invoice->id]) }}" class="btn btn-sm btn-primary">
                <i class="fas fa-edit"></i>
              </a>
              @endif
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="8" class="text-center">No invoices found for this project</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection