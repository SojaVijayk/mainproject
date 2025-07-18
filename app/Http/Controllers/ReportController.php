<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\MaintenanceRecord;
use App\Models\Ticket;
use App\Models\AssetHistory;
use App\Models\Consumable;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AssetsExport;
use App\Exports\MaintenanceExport;
use App\Exports\TicketsExport;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function assets(Request $request)
    {
        $assets = Asset::with(['model', 'status', 'assignedUser', 'department'])
            ->filter($request->all())
            ->get();

        if ($request->has('export')) {
            return Excel::download(new AssetsExport($assets), 'assets-report.xlsx');
        }

        return view('reports.assets', compact('assets'));
    }

    public function maintenance(Request $request)
    {
        $maintenance = MaintenanceRecord::with(['asset', 'user'])
            ->filter($request->all())
            ->get();

        if ($request->has('export')) {
            return Excel::download(new MaintenanceExport($maintenance), 'maintenance-report.xlsx');
        }

        return view('reports.maintenance', compact('maintenance'));
    }

    public function tickets(Request $request)
    {
        $tickets = Ticket::with(['asset', 'user', 'assignedTo'])
            ->filter($request->all())
            ->get();

        if ($request->has('export')) {
            return Excel::download(new TicketsExport($tickets), 'tickets-report.xlsx');
        }

        return view('reports.tickets', compact('tickets'));
    }

    public function audit(Request $request)
    {
        $history = AssetHistory::with(['asset', 'user'])
            ->filter($request->all())
            ->latest()
            ->get();

        return view('reports.audit', compact('history'));
    }

    public function depreciation(Request $request)
    {
        $assets = Asset::with(['model', 'status'])
            ->whereNotNull('purchase_date')
            ->whereNotNull('purchase_cost')
            ->get()
            ->map(function($asset) {
                $age = Carbon::parse($asset->purchase_date)->diffInYears(now());
                $depreciationRate = 0.25; // 25% per year
                $depreciatedValue = $asset->purchase_cost * pow((1 - $depreciationRate), $age);

                return [
                    'asset' => $asset,
                    'age' => $age,
                    'depreciated_value' => max($depreciatedValue, 0),
                    'depreciation_rate' => $depreciationRate * 100
                ];
            });

        return view('reports.depreciation', compact('assets'));
    }
}
