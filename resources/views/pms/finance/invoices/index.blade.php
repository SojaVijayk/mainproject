@extends('layouts/layoutMaster')

@section('title', 'Invoice Management')

@section('content')
@php
use \App\Models\PMS\Project;
use \App\Models\PMS\Invoice;
use \App\Models\User;
use \App\Models\Client;
@endphp

<div class="card">
  <div class="card-header">
    <div class="d-flex justify-content-between align-items-center">
      <h5 class="mb-0">Invoices by Project</h5>
      <button class="btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#filterCollapse">
        <i class="fas fa-filter me-1"></i> Filters
      </button>
    </div>
  </div>

  <!-- Filters Section -->
  <div class="collapse" id="filterCollapse">
    <div class="card-body border-bottom">
      <form method="GET" action="{{ route('pms.finance.invoices.index') }}">
        <div class="row">
          <!-- Project Filter -->
          <div class="col-md-3 mb-3">
            <label class="form-label">Project</label>
            <select class="form-select" name="project_id">
              <option value="">All Projects</option>
              @foreach($allProjects as $project)
              <option value="{{ $project->id }}" {{ request('project_id')==$project->id ? 'selected' : '' }}>
                {{ $project->title }} ({{ $project->project_code }})
              </option>
              @endforeach
            </select>
          </div>

          <!-- Client Filter -->
          <div class="col-md-3 mb-3">
            <label class="form-label">Client</label>
            <select class="form-select" name="client_id">
              <option value="">All Clients</option>
              @foreach($clients as $client)
              <option value="{{ $client->id }}" {{ request('client_id')==$client->id ? 'selected' : '' }}>
                {{ $client->client_name }}
              </option>
              @endforeach
            </select>
          </div>

          <!-- Project Investigator Filter -->
          <div class="col-md-3 mb-3">
            <label class="form-label">Project Investigator</label>
            <select class="form-select" name="investigator_id">
              <option value="">All Investigators</option>
              @foreach($investigators as $investigator)
              <option value="{{ $investigator->id }}" {{ request('investigator_id')==$investigator->id ? 'selected' : ''
                }}>
                {{ $investigator->name }}
              </option>
              @endforeach
            </select>
          </div>

          <!-- Requested By Filter -->
          <div class="col-md-3 mb-3">
            <label class="form-label">Requested By</label>
            <select class="form-select" name="requested_by">
              <option value="">All Users</option>
              @foreach($users as $user)
              <option value="{{ $user->id }}" {{ request('requested_by')==$user->id ? 'selected' : '' }}>
                {{ $user->name }}
              </option>
              @endforeach
            </select>
          </div>

          <!-- Invoice Date Range -->
          <div class="col-md-3 mb-3">
            <label class="form-label">Invoice Date From</label>
            <input type="date" class="form-control" name="invoice_date_from" value="{{ request('invoice_date_from') }}">
          </div>

          <div class="col-md-3 mb-3">
            <label class="form-label">Invoice Date To</label>
            <input type="date" class="form-control" name="invoice_date_to" value="{{ request('invoice_date_to') }}">
          </div>

          <!-- Status Filter -->
          <div class="col-md-3 mb-3">
            <label class="form-label">Status</label>
            <select class="form-select" name="status">
              <option value="">All Status</option>
              <option value="0" {{ request('status')=='0' ? 'selected' : '' }}>Draft</option>
              <option value="1" {{ request('status')=='1' ? 'selected' : '' }}>Sent</option>
              <option value="2" {{ request('status')=='2' ? 'selected' : '' }}>Paid</option>
              <option value="3" {{ request('status')=='3' ? 'selected' : '' }}>Overdue</option>
              <option value="4" {{ request('status')=='4' ? 'selected' : '' }}>Cancelled</option>
            </select>
          </div>

          <!-- Action Buttons -->
          <div class="col-md-3 mb-3 d-flex align-items-end">
            <button type="submit" class="btn btn-primary me-2">
              <i class="fas fa-search me-1"></i> Apply Filters
            </button>
            <a href="{{ route('pms.finance.invoices.index') }}" class="btn btn-outline-secondary">
              <i class="fas fa-times me-1"></i> Clear
            </a>
          </div>
        </div>
      </form>
    </div>
  </div>

  <div class="card-body">
    <!-- Summary Stats -->
    @if(request()->anyFilled(['project_id', 'client_id', 'investigator_id', 'requested_by', 'invoice_date_from',
    'invoice_date_to', 'status']))
    <div class="row mb-4">
      <div class="col-12">
        <div class="alert alert-info">
          <strong>Filtered Results:</strong>
          Showing {{ $filteredInvoices->count() }} invoices
          @if(request('project_id'))
          | Project: {{ Project::find(request('project_id'))?->title }}
          @endif
          @if(request('client_id'))
          | Client: {{ Client::find(request('client_id'))?->name }}
          @endif
          @if(request('investigator_id'))
          | Investigator: {{ User::find(request('investigator_id'))?->name }}
          @endif
          @if(request('requested_by'))
          | Requested By: {{ User::find(request('requested_by'))?->name }}
          @endif
          @if(request('invoice_date_from') || request('invoice_date_to'))
          | Date Range: {{ request('invoice_date_from') }} to {{ request('invoice_date_to') }}
          @endif
          @if(request('status'))
          | Status: {{ Invoice::find(1)->getStatusNameAttribute() }}
          @endif
        </div>
      </div>
    </div>
    @endif

    <div class="accordion" id="invoiceAccordion">
      @foreach($filteredProjects as $project)
      <div class="accordion-item">
        <h2 class="accordion-header" id="heading{{ $project->id }}">
          <button class="accordion-button" type="button" data-bs-toggle="collapse"
            data-bs-target="#collapse{{ $project->id }}" aria-expanded="true"
            aria-controls="collapse{{ $project->id }}">
            <div class="d-flex justify-content-between w-100 me-3">
              <div class="d-flex flex-column text-start">
                <span class="fw-bold">{{ $project->title }}</span>
                <small class="text-muted">
                  Project Code: {{ $project->project_code }} |
                  Client: {{ $project->requirement->client->name ?? 'N/A' }} |
                  Investigator: {{ $project->investigator->name ?? 'N/A' }}
                </small>
              </div>
              <span class="badge bg-primary rounded-pill ms-2">
                {{ $project->invoices->count() }} invoices
              </span>
            </div>
          </button>
        </h2>
        <div id="collapse{{ $project->id }}" class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}"
          aria-labelledby="heading{{ $project->id }}" data-bs-parent="#invoiceAccordion">
          <div class="accordion-body p-0">
            @if($project->invoices->count() > 0)
            <div class="table-responsive">
              <table class="table table-hover">
                <thead>
                  <tr>
                    <th>Invoice #</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Amount</th>
                    <th>Balance</th>
                    <th>Requested By</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($project->invoices as $invoice)
                  <tr>
                    <td>
                      @if($invoice->invoice_number)
                      {{ $invoice->invoice_number }}
                      @else
                      <span class="text-muted">Draft</span>
                      @endif
                    </td>
                    <td>
                      <span class="badge bg-{{$invoice->invoice_type == 1 ? 'primary' : 'success'}}">
                        {{ $invoice->invoice_type == 1 ? 'Proforma Invoice' : 'Tax Invoice' }}
                      </span>
                    </td>
                    <td>
                      <span class="badge bg-{{ $invoice->status_badge_color }}">
                        {{ $invoice->status_name }}
                      </span>
                    </td>
                    <td>{{ $invoice->invoice_date?->format('M d, Y') ?? '-' }}</td>
                    <td>₹{{ number_format($invoice->amount, 2) }}</td>
                    <td>
                      @if($invoice->status == Invoice::STATUS_PAID)
                      <span class="text-success">Paid</span>
                      @else
                      ₹{{ number_format($invoice->balance_amount, 2) }}
                      @endif
                    </td>
                    <td>
                      {{ $invoice->requestedBy->name ?? 'N/A' }}
                    </td>
                    <td>
                      <div class="btn-group">
                        <a href="{{ route('pms.finance.invoices.show', $invoice->id) }}" class="btn btn-sm btn-icon"
                          title="View">
                          <i class="fas fa-eye"></i>
                        </a>
                        @if($invoice->status == Invoice::STATUS_DRAFT)
                        <a href="{{ route('pms.finance.invoices.process', $invoice->id) }}" class="btn btn-sm btn-icon"
                          title="Edit">
                          <i class="fas fa-edit"></i>
                        </a>
                        @endif
                      </div>
                    </td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
            @else
            <div class="text-center py-3">
              <p class="text-muted">No invoices found for this project with current filters.</p>
            </div>
            @endif
          </div>
        </div>
      </div>
      @endforeach

      @if($filteredProjects->count() == 0)
      <div class="text-center py-4">
        <p class="text-muted">No invoices found matching your filters.</p>
        <a href="{{ route('pms.finance.invoices.index') }}" class="btn btn-primary">Clear Filters</a>
      </div>
      @endif
    </div>
  </div>
</div>
@endsection