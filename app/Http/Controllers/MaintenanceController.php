<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceRecord;
use App\Models\Asset;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\StoreMaintenanceRecordRequest;
use App\Http\Requests\UpdateMaintenanceRecordRequest;

class MaintenanceController extends Controller
{
    public function index()
    {
        $maintenanceRecords = MaintenanceRecord::with(['asset', 'user'])
            ->latest()
            ->paginate(20);

        return view('maintenance.index', compact('maintenanceRecords'));
    }

    public function create()
    {
        $assets = Asset::all();
        $users = User::all();
        return view('maintenance.create', compact('assets', 'users'));
    }

    public function store(StoreMaintenanceRecordRequest $request)
    {
        MaintenanceRecord::create($request->validated());

        return redirect()->route('maintenance.index')
            ->with('success', 'Maintenance record created successfully');
    }

    public function show(MaintenanceRecord $maintenanceRecord)
    {
        return view('maintenance.show', compact('maintenanceRecord'));
    }

    public function edit(MaintenanceRecord $maintenanceRecord)
    {
        $assets = Asset::all();
        $users = User::all();
        return view('maintenance.edit', compact('maintenanceRecord', 'assets', 'users'));
    }

    public function update(UpdateMaintenanceRecordRequest $request, MaintenanceRecord $maintenanceRecord)
    {
        $maintenanceRecord->update($request->validated());
        return redirect()->route('maintenance.index')
            ->with('success', 'Maintenance record updated successfully');
    }

    public function destroy(MaintenanceRecord $maintenanceRecord)
    {
        $maintenanceRecord->delete();
        return redirect()->route('maintenance.index')
            ->with('success', 'Maintenance record deleted successfully');
    }
}
