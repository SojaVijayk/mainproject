<?php

namespace App\Http\Controllers;

use App\Models\AssetStatus;
use Illuminate\Http\Request;
use App\Http\Requests\StoreAssetStatusRequest;
use App\Http\Requests\UpdateAssetStatusRequest;

class AssetStatusController extends Controller
{
    public function index()
    {
        $statuses = AssetStatus::latest()->paginate(20);
        return view('asset-statuses.index', compact('statuses'));
    }

    public function create()
    {
        return view('asset-statuses.create');
    }

    public function store(StoreAssetStatusRequest $request)
    {
        AssetStatus::create($request->validated());
        return redirect()->route('asset-statuses.index')
            ->with('success', 'Status created successfully');
    }

    public function show(AssetStatus $assetStatus)
    {
        return view('asset-statuses.show', compact('assetStatus'));
    }

    public function edit(AssetStatus $assetStatus)
    {
        return view('asset-statuses.edit', compact('assetStatus'));
    }

    public function update(UpdateAssetStatusRequest $request, AssetStatus $assetStatus)
    {
        $assetStatus->update($request->validated());
        return redirect()->route('asset-statuses.index')
            ->with('success', 'Status updated successfully');
    }

    public function destroy(AssetStatus $assetStatus)
    {
        $assetStatus->delete();
        return redirect()->route('asset-statuses.index')
            ->with('success', 'Status deleted successfully');
    }
}
