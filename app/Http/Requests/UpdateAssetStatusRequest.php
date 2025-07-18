<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAssetStatusRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|max:255|unique:asset_statuses,name,' . $this->asset_status->id,
            'color' => 'required|string|max:20',
            'notes' => 'nullable|string'
        ];
    }
}
