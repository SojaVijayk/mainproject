<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateManufacturerRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|max:255|unique:manufacturers,name,' . $this->manufacturer->id,
            'support_email' => 'nullable|email',
            'support_phone' => 'nullable|string|max:20',
            'support_url' => 'nullable|url'
        ];
    }
}
