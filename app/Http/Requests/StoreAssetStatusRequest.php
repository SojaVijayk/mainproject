<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAssetStatusRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|unique:asset_statuses|max:255',
            'color' => 'required|string|max:20',
            'notes' => 'nullable|string'
        ];
    }
}
