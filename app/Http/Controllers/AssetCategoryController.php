<?php

namespace App\Http\Controllers;

use App\Models\AssetCategory;
use Illuminate\Http\Request;
use App\Http\Requests\StoreAssetCategoryRequest;
use App\Http\Requests\UpdateAssetCategoryRequest;

class AssetCategoryController extends Controller
{
    public function index()
    {
        $categories = AssetCategory::latest()->paginate(20);
        return view('asset-categories.index', compact('categories'));
    }

    public function create()
    {
        return view('asset-categories.create');
    }

    public function store(StoreAssetCategoryRequest $request)
    {
        AssetCategory::create($request->validated());
        return redirect()->route('asset-categories.index')
            ->with('success', 'Category created successfully');
    }

    public function show(AssetCategory $assetCategory)
    {
        return view('asset-categories.show', compact('assetCategory'));
    }

    public function edit(AssetCategory $assetCategory)
    {
        return view('asset-categories.edit', compact('assetCategory'));
    }

    public function update(UpdateAssetCategoryRequest $request, AssetCategory $assetCategory)
    {
        $assetCategory->update($request->validated());
        return redirect()->route('asset-categories.index')
            ->with('success', 'Category updated successfully');
    }

    public function destroy(AssetCategory $assetCategory)
    {
        $assetCategory->delete();
        return redirect()->route('asset-categories.index')
            ->with('success', 'Category deleted successfully');
    }
}
