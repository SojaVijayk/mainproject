@extends('layouts/layoutMaster')

@section('content')
<div class="container-fluid">
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="mb-0">Expenses</h5>
          <div>
            <a href="{{ route('pms.expenses.create') }}" class="btn btn-primary btn-sm">Add Expense</a>
            <a href="{{ route('pms.expense-categories.index') }}" class="btn btn-secondary btn-sm">Categories</a>
            <a href="{{ route('pms.vendors.index') }}" class="btn btn-secondary btn-sm">Vendors</a>
          </div>
        </div>
        <div class="card-body">
          <form method="GET" action="{{ route('pms.expenses.index') }}" class="mb-4">
            <div class="row">
              <div class="col-md-2">
                <select name="project_id" class="form-control">
                  <option value="">All Projects</option>
                  @foreach($projects as $project)
                  <option value="{{ $project->id }}" {{ request('project_id')==$project->id ? 'selected' : '' }}>{{
                    $project->title }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-2">
                <select name="category_id" class="form-control">
                  <option value="">All Categories</option>
                  @foreach($categories as $category)
                  <option value="{{ $category->id }}" {{ request('category_id')==$category->id ? 'selected' : '' }}>{{
                    $category->name }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-2">
                <select name="vendor_id" class="form-control">
                  <option value="">All Vendors</option>
                  @foreach($vendors as $vendor)
                  <option value="{{ $vendor->id }}" {{ request('vendor_id')==$vendor->id ? 'selected' : '' }}>{{
                    $vendor->name }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-2">
                <select name="payment_mode" class="form-control">
                  <option value="">All Payment Modes</option>
                  @foreach($paymentModes as $mode)
                  <option value="{{ $mode }}" {{ request('payment_mode')==$mode ? 'selected' : '' }}>{{
                    ucwords(str_replace('_', ' ', $mode)) }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-2">
                <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}"
                  placeholder="Start Date">
              </div>
              <div class="col-md-2">
                <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}"
                  placeholder="End Date">
              </div>
              <div class="col-md-12 mt-2">
                <button type="submit" class="btn btn-primary">Filter</button>
                <a href="{{ route('pms.expenses.index') }}" class="btn btn-secondary">Reset</a>
                <a href="{{ route('pms.expenses.export.excel', request()->all()) }}" class="btn btn-success">Export
                  Excel</a>
                <a href="{{ route('pms.expenses.export.pdf', request()->all()) }}" class="btn btn-danger">Export PDF</a>
              </div>
            </div>
          </form>

          <div class="table-responsive">
            <table class="table table-bordered">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Project Code</th>
                  <th>Project</th>
                  <th>Category</th>
                  <th>Vendor</th>
                  <th>Amount</th>
                  <th>Tax</th>
                  <th>Total</th>
                  <th>Payment Mode</th>
                  <th>Payment Date</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                @foreach($expenses as $expense)
                <tr>
                  <td>{{ $expense->id }}</td>
                  <td> {{$expense->project->project_code }}</td>
                  <td>{{ $expense->project->title }}</td>

                  <td>{{ $expense->category->name }}</td>
                  <td>{{ $expense->vendor->name }}</td>
                  <td>{{ number_format($expense->amount, 2) }}</td>
                  <td>{{ number_format($expense->tax, 2) }}</td>
                  <td>{{ number_format($expense->total_amount, 2) }}</td>
                  <td>{{ ucwords(str_replace('_', ' ', $expense->payment_mode)) }}</td>
                  <td>{{ $expense->payment_date->format('M d, Y') }}</td>
                  <td>
                    <a href="{{ route('pms.expenses.show', $expense) }}" class="btn btn-info btn-sm">View</a>
                    <a href="{{ route('pms.expenses.edit', $expense) }}" class="btn btn-primary btn-sm">Edit</a>
                    <form action="{{ route('pms.expenses.destroy', $expense) }}" method="POST" class="d-inline">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-danger btn-sm"
                        onclick="return confirm('Are you sure?')">Delete</button>
                    </form>
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>

          <div class="d-flex justify-content-center">
            {{ $expenses->links() }}
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection