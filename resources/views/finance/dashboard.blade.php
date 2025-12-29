@extends('layouts/layoutMaster')
@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}">

@endsection
@section('vendor-script')
<script src="{{asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js')}}"></script>
<script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>

@endsection
@section('page-script')
<script>
  $('.select2').select2({ width: 'resolve' });
</script>
@endsection

@section('content')
<div class="row">
  <!-- Total Balance Card -->
  <div class="col-md-4 mb-4">
    <div class="card h-100">
      <div class="card-body">
        <h5 class="card-title">Total Balance</h5>
        <h2 class="text-primary mb-0">₹{{ number_format($totalBalance, 2) }}</h2>
        <small class="text-muted">Across all accounts</small>
      </div>
    </div>
  </div>
  <!-- Today's Inflow Card -->
  <div class="col-md-4 mb-4">
    <div class="card h-100">
      <div class="card-body">
        <h5 class="card-title">Today's Inflow</h5>
        <h2 class="text-success mb-0">₹{{ number_format($todayInflow, 2) }}</h2>
        <small class="text-muted">Credits today</small>
      </div>
    </div>
  </div>
  <!-- Today's Outflow Card -->
  <div class="col-md-4 mb-4">
    <div class="card h-100">
      <div class="card-body">
        <h5 class="card-title">Today's Outflow</h5>
        <h2 class="text-danger mb-0">₹{{ number_format($todayOutflow, 2) }}</h2>
        <small class="text-muted">Debits today</small>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <!-- Accounts Summary -->
  <div class="col-md-6 mb-4">
    <div class="card h-100">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Bank Accounts</h5>
        <a href="{{ route('pms.finance.accounts.index') }}" class="btn btn-sm btn-primary">Manage</a>
      </div>
      <div class="table-responsive text-nowrap">
        <table class="table">
          <thead>
            <tr>
              <th>Bank</th>
              <th>Number</th>
              <th>Balance</th>
            </tr>
          </thead>
          <tbody>
            @foreach($accounts as $account)
            <tr>
              <td>{{ $account->bank_name }}</td>
              <td>{{ $account->account_number }}</td>
              <td>₹{{ number_format($account->current_balance, 2) }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Recent Transactions -->
  <div class="col-md-6 mb-4">
    <div class="card h-100">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Recent Transactions</h5>
        <a href="{{ route('pms.finance.transactions.index') }}" class="btn btn-sm btn-primary">View All</a>
      </div>
      <div class="table-responsive text-nowrap">
        <table class="table">
          <thead>
            <tr>
              <th>Date</th>
              <th>Description</th>
              <th>Amount</th>
            </tr>
          </thead>
          <tbody>
            @foreach($recentTransactions as $transaction)
            <tr>
              <td>{{ $transaction->transaction_date->format('d M') }}</td>
              <td>{{ $transaction->description }}</td>
              <td class="{{ $transaction->type == 'credit' ? 'text-success' : 'text-danger' }}">
                {{ $transaction->type == 'credit' ? '+' : '-' }}₹{{ number_format($transaction->amount, 2) }}
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection