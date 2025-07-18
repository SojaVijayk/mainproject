<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAssetModelRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|max:255',
            'category_id' => 'required|exists:asset_categories,id',
            'manufacturer_id' => 'required|exists:manufacturers,id',
            'model_number' => 'nullable|string|max:100',
            'image' => 'nullable|image|max:2048',
            'is_consumable' => 'boolean'
        ];
    }
}
