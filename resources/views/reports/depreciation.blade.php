@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-6">
            <h1>Depreciation Report</h1>
        </div>
        <div class="col-md-6 text-right">
            <a href="{{ route('reports.depreciation') }}?export=excel" class="btn btn-success">
                <i class="fas fa-file-excel"></i> Export to Excel
            </a>
            <a href="{{ route('reports.depreciation') }}?export=pdf" class="btn btn-danger">
                <i class="fas fa-file-pdf"></i> Export to PDF
            </a>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-filter"></i> Filters
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('reports.depreciation') }}">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="category_id">Category</label>
                            <select class="form-control" name="category_id">
                                <option value="">All Categories</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="depreciation_years">Depreciation Period (Years)</label>
                            <select class="form-control" name="depreciation_years">
                                <option value="3" {{ request('depreciation_years', 3) == 3 ? 'selected' : '' }}>3 Years</option>
                                <option value="5" {{ request('depreciation_years', 3) == 5 ? 'selected' : '' }}>5 Years</option>
                                <option value="7" {{ request('depreciation_years', 3) == 7 ? 'selected' : '' }}>7 Years</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="date">As of Date</label>
                            <input type="date" class="form-control" name="date" value="{{ request('date', now()->format('Y-m-d')) }}">
                        </div>
                    </div>
                </div>
                <div class="text-right">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                    <a href="{{ route('reports.depreciation') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <i class="fas fa-table"></i> Depreciation Report
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Asset Tag</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Purchase Date</th>
                            <th>Purchase Cost</th>
                            <th>Depreciation Period</th>
                            <th>Current Value</th>
                            <th>Depreciated Amount</th>
                            <th>Months Remaining</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($assets as $asset)
                        @php
                            $depreciationYears = request('depreciation_years', 3);
                            $depreciationMonths = $depreciationYears * 12;
                            $monthsPassed = $asset->purchase_date->diffInMonths(now());
                            $depreciationPerMonth = $asset->purchase_cost / $depreciationMonths;
                            
                            if ($monthsPassed >= $depreciationMonths) {
                                $currentValue = 0;
                                $depreciatedAmount = $asset->purchase_cost;
                                $monthsRemaining = 0;
                            } else {
                                $currentValue = $asset->purchase_cost - ($depreciationPerMonth * $monthsPassed);
                                $depreciatedAmount = $depreciationPerMonth * $monthsPassed;
                                $monthsRemaining = $depreciationMonths - $monthsPassed;
                            }
                        @endphp
                        <tr>
                            <td>{{ $asset->asset_tag }}</td>
                            <td>{{ $asset->name }}</td>
                            <td>{{ $asset->model->category->name }}</td>
                            <td>{{ $asset->purchase_date->format('Y-m-d') }}</td>
                            <td>${{ number_format($asset->purchase_cost, 2) }}</td>
                            <td>{{ $depreciationYears }} years</td>
                            <td>${{ number_format($currentValue, 2) }}</td>
                            <td>${{ number_format($depreciatedAmount, 2) }}</td>
                            <td>{{ $monthsRemaining }} months</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5>Summary</h5>
                        </div>
                        <div class="card-body">
                            <p><strong>Total Assets:</strong> {{ $assets->count() }}</p>
                            <p><strong>Total Purchase Value:</strong> ${{ number_format($assets->sum('purchase_cost'), 2) }}</p>
                            <p><strong>Total Current Value:</strong> ${{ number_format($assets->sum(function($asset) use ($depreciationYears) {
                                $monthsPassed = $asset->purchase_date->diffInMonths(now());
                                $depreciationMonths = $depreciationYears * 12;
                                $depreciationPerMonth = $asset->purchase_cost / $depreciationMonths;
                                return $monthsPassed >= $depreciationMonths ? 0 : $asset->purchase_cost - ($depreciationPerMonth * $monthsPassed);
                            }), 2) }}</p>
                            <p><strong>Total Depreciated Value:</strong> ${{ number_format($assets->sum(function($asset) use ($depreciationYears) {
                                $monthsPassed = $asset->purchase_date->diffInMonths(now());
                                $depreciationMonths = $depreciationYears * 12;
                                $depreciationPerMonth = $asset->purchase_cost / $depreciationMonths;
                                return $monthsPassed >= $depreciationMonths ? $asset->purchase_cost : $depreciationPerMonth * $monthsPassed;
                            }), 2) }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5>By Category</h5>
                        </div>
                        <div class="card-body">
                            @foreach($categorySummary as $category => $data)
                                <p><strong>{{ $category }}:</strong>
                                    {{ $data['count'] }} assets, 
                                    Current Value: ${{ number_format($data['current_value'], 2) }},
                                    Depreciated: ${{ number_format($data['depreciated'], 2) }}
                                </p>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection