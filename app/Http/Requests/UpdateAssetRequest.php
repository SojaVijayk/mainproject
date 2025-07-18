<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAssetRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'asset_tag' => 'required|max:50|unique:assets,asset_tag,' . $this->asset->id,
            'name' => 'required|max:255',
            'description' => 'nullable|string',
            'model_id' => 'required|exists:asset_models,id',
            'status_id' => 'required|exists:asset_statuses,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'location_id' => 'required|exists:locations,id',
            'purchase_cost' => 'required|numeric|min:0',
            'purchase_date' => 'required|date',
            'warranty_expiry' => 'nullable|date|after_or_equal:purchase_date',
            'serial_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'assigned_to' => 'nullable|exists:users,id',
            'assigned_type' => 'nullable|in:user,department',
            'department_id' => 'nullable|exists:departments,id',
            'floor' => 'nullable|string|max:50'
        ];
    }
}
