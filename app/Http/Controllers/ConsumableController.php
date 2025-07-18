<?php

namespace App\Http\Controllers;

use App\Models\Consumable;
use App\Models\AssetCategory;
use App\Models\Supplier;
use App\Models\User;
use App\Models\Department;
use App\Models\ConsumableAssignment;
use Illuminate\Http\Request;
use App\Http\Requests\StoreConsumableRequest;
use App\Http\Requests\UpdateConsumableRequest;

class ConsumableController extends Controller
{
    public function index()
    {
        $consumables = Consumable::with(['category', 'supplier'])
            ->latest()
            ->paginate(20);

        return view('consumables.index', compact('consumables'));
    }

    public function create()
    {
        $categories = AssetCategory::whereHas('models', function($q) {
            $q->where('is_consumable', true);
        })->get();

        $suppliers = Supplier::all();

        return view('consumables.create', compact('categories', 'suppliers'));
    }

    public function store(StoreConsumableRequest $request)
    {
        Consumable::create($request->validated());
        return redirect()->route('consumables.index')
            ->with('success', 'Consumable created successfully');
    }

    public function show(Consumable $consumable)
    {
        $consumable->load(['category', 'supplier', 'assignments.user', 'assignments.department']);
        return view('consumables.show', compact('consumable'));
    }

    public function edit(Consumable $consumable)
    {
        $categories = AssetCategory::whereHas('models', function($q) {
            $q->where('is_consumable', true);
        })->get();

        $suppliers = Supplier::all();

        return view('consumables.edit', compact('consumable', 'categories', 'suppliers'));
    }

    public function update(UpdateConsumableRequest $request, Consumable $consumable)
    {
        $consumable->update($request->validated());
        return redirect()->route('consumables.index')
            ->with('success', 'Consumable updated successfully');
    }

    public function destroy(Consumable $consumable)
    {
        $consumable->delete();
        return redirect()->route('consumables.index')
            ->with('success', 'Consumable deleted successfully');
    }

    public function checkout(Consumable $consumable)
    {
        $users = User::all();
        $departments = Department::all();

        return view('consumables.checkout', compact('consumable', 'users', 'departments'));
    }

    public function processCheckout(Request $request, Consumable $consumable)
    {
        $request->validate([
            'assigned_type' => 'required|in:user,department',
            'user_id' => 'required_if:assigned_type,user',
            'department_id' => 'required_if:assigned_type,department',
            'floor' => 'nullable|string|max:255',
            'quantity' => 'required|integer|min:1|max:' . $consumable->available_quantity,
            'notes' => 'nullable|string'
        ]);

        ConsumableAssignment::create([
            'consumable_id' => $consumable->id,
            'quantity' => $request->quantity,
            'user_id' => $request->assigned_type === 'user' ? $request->user_id : null,
            'department_id' => $request->assigned_type === 'department' ? $request->department_id : null,
            'floor' => $request->floor,
            'notes' => $request->notes
        ]);

        return redirect()->route('consumables.show', $consumable)
            ->with('success', 'Consumable checked out successfully');
    }
}
