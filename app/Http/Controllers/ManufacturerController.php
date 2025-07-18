<?php

namespace App\Http\Controllers;

use App\Models\Manufacturer;
use Illuminate\Http\Request;
use App\Http\Requests\StoreManufacturerRequest;
use App\Http\Requests\UpdateManufacturerRequest;

class ManufacturerController extends Controller
{
    public function index()
    {
        $manufacturers = Manufacturer::latest()->paginate(20);
        return view('manufacturers.index', compact('manufacturers'));
    }

    public function create()
    {
        return view('manufacturers.create');
    }

    public function store(StoreManufacturerRequest $request)
    {
        Manufacturer::create($request->validated());
        return redirect()->route('manufacturers.index')
            ->with('success', 'Manufacturer created successfully');
    }

    public function show(Manufacturer $manufacturer)
    {
        return view('manufacturers.show', compact('manufacturer'));
    }

    public function edit(Manufacturer $manufacturer)
    {
        return view('manufacturers.edit', compact('manufacturer'));
    }

    public function update(UpdateManufacturerRequest $request, Manufacturer $manufacturer)
    {
        $manufacturer->update($request->validated());
        return redirect()->route('manufacturers.index')
            ->with('success', 'Manufacturer updated successfully');
    }

    public function destroy(Manufacturer $manufacturer)
    {
        $manufacturer->delete();
        return redirect()->route('manufacturers.index')
            ->with('success', 'Manufacturer deleted successfully');
    }
}
