<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetModel;
use App\Models\AssetCategory;
use App\Models\AssetStatus;
use App\Models\Supplier;
use App\Models\Location;
use App\Models\User;
use App\Models\Department;
use App\Models\AssetHistory;
use Illuminate\Http\Request;
use App\Http\Requests\StoreAssetRequest;
use App\Http\Requests\UpdateAssetRequest;

class AssetController extends Controller
{
    public function index()
    {
        $pageConfigs = ['myLayout' => 'horizontal'];
        $assets = Asset::with(['model', 'status', 'assignedUser', 'department'])
            // ->filter(request()->all())
            ->paginate(25);

        $statuses = AssetStatus::all();
        $categories = AssetCategory::all();

        return view('assets.index', compact('assets', 'statuses', 'categories'),['pageConfigs'=> $pageConfigs]);
    }

    public function create()
    {
        $models = AssetModel::all();
        $statuses = AssetStatus::all();
        $suppliers = Supplier::all();
        $locations = Location::all();
        $users = User::all();
        $departments = Department::all();
         $pageConfigs = ['myLayout' => 'horizontal'];
        return view('assets.create', compact(
            'models', 'statuses', 'suppliers',
            'locations', 'users', 'departments'
        ),['pageConfigs'=> $pageConfigs]);
    }

    public function store(StoreAssetRequest $request)
    {
        $asset = Asset::create($request->validated());

        AssetHistory::create([
            'asset_id' => $asset->id,
            'user_id' => auth()->id(),
            'action' => 'Asset Created',
            'details' => 'Asset was created in the system'
        ]);

        return redirect()->route('assets.show', $asset)
            ->with('success', 'Asset created successfully');
    }

    public function show(Asset $asset)
    {
        $asset->load([
            'model.category',
            'model.manufacturer',
            'status',
            'supplier',
            'location',
            'assignedUser',
            'department',
            'maintenance',
            'tickets' => function($q) {
                $q->latest()->limit(5);
            },
            'history' => function($q) {
                $q->latest()->limit(10);
            }
        ]);

        return view('assets.show', compact('asset'));
    }

    public function edit(Asset $asset)
    {
        $models = AssetModel::all();
        $statuses = AssetStatus::all();
        $suppliers = Supplier::all();
        $locations = Location::all();
        $users = User::all();
        $departments = Department::all();

        return view('assets.edit', compact(
            'asset', 'models', 'statuses',
            'suppliers', 'locations', 'users', 'departments'
        ));
    }

    public function update(UpdateAssetRequest $request, Asset $asset)
    {
        $changes = $asset->getDirty();

        $asset->update($request->validated());

        if(count($changes)) {
            AssetHistory::create([
                'asset_id' => $asset->id,
                'user_id' => auth()->id(),
                'action' => 'Asset Updated',
                'details' => 'Asset details were modified',
                'changes' => $changes
            ]);
        }

        return redirect()->route('assets.show', $asset)
            ->with('success', 'Asset updated successfully');
    }

    public function destroy(Asset $asset)
    {
        $asset->delete();

        return redirect()->route('assets.index')
            ->with('success', 'Asset deleted successfully');
    }

    public function checkout(Asset $asset)
    {
        $users = User::all();
        $departments = Department::all();

        return view('assets.checkout', compact('asset', 'users', 'departments'));
    }

    public function processCheckout(Request $request, Asset $asset)
    {
        $request->validate([
            'assigned_type' => 'required|in:user,department',
            'user_id' => 'required_if:assigned_type,user',
            'department_id' => 'required_if:assigned_type,department',
            'floor' => 'nullable|string|max:255',
            'notes' => 'nullable|string'
        ]);

        $asset->update([
            'assigned_type' => $request->assigned_type,
            'assigned_to' => $request->assigned_type === 'user' ? $request->user_id : null,
            'department_id' => $request->assigned_type === 'department' ? $request->department_id : null,
            'floor' => $request->floor,
            'notes' => $request->notes,
            'status_id' => 2 // Assuming 2 is "Assigned" status
        ]);

        AssetHistory::create([
            'asset_id' => $asset->id,
            'user_id' => auth()->id(),
            'action' => 'Asset Checked Out',
            'details' => 'Asset was checked out to ' .
                ($request->assigned_type === 'user' ?
                    User::find($request->user_id)->name :
                    Department::find($request->department_id)->name),
            'changes' => $asset->getChanges()
        ]);

        return redirect()->route('assets.show', $asset)
            ->with('success', 'Asset checked out successfully');
    }

    public function checkin(Asset $asset)
    {
        $asset->update([
            'assigned_type' => null,
            'assigned_to' => null,
            'department_id' => null,
            'floor' => null,
            'status_id' => 1 // Assuming 1 is "Available" status
        ]);

        AssetHistory::create([
            'asset_id' => $asset->id,
            'user_id' => auth()->id(),
            'action' => 'Asset Checked In',
            'details' => 'Asset was checked in from previous assignment'
        ]);

        return redirect()->route('assets.show', $asset)
            ->with('success', 'Asset checked in successfully');
    }
}