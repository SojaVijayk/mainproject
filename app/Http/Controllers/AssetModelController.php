<?php

namespace App\Http\Controllers;

use App\Models\AssetModel;
use App\Models\AssetCategory;
use App\Models\Manufacturer;
use Illuminate\Http\Request;
use App\Http\Requests\StoreAssetModelRequest;
use App\Http\Requests\UpdateAssetModelRequest;

class AssetModelController extends Controller
{
    public function index()
    {
        $models = AssetModel::with(['category', 'manufacturer'])->latest()->paginate(20);
        return view('asset-models.index', compact('models'));
    }

    public function create()
    {
        $categories = AssetCategory::all();
        $manufacturers = Manufacturer::all();
        return view('asset-models.create', compact('categories', 'manufacturers'));
    }

    public function store(StoreAssetModelRequest $request)
    {
        AssetModel::create($request->validated());
        return redirect()->route('asset-models.index')
            ->with('success', 'Model created successfully');
    }

    public function show(AssetModel $assetModel)
    {
        return view('asset-models.show', compact('assetModel'));
    }

    public function edit(AssetModel $assetModel)
    {
        $categories = AssetCategory::all();
        $manufacturers = Manufacturer::all();
        return view('asset-models.edit', compact('assetModel', 'categories', 'manufacturers'));
    }

    public function update(UpdateAssetModelRequest $request, AssetModel $assetModel)
    {
        $assetModel->update($request->validated());
        return redirect()->route('asset-models.index')
            ->with('success', 'Model updated successfully');
    }

    public function destroy(AssetModel $assetModel)
    {
        $assetModel->delete();
        return redirect()->route('asset-models.index')
            ->with('success', 'Model deleted successfully');
    }
}
