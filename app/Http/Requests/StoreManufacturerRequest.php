<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreManufacturerRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|unique:manufacturers|max:255',
            'support_email' => 'nullable|email',
            'support_phone' => 'nullable|string|max:20',
            'support_url' => 'nullable|url'
        ];
    }
}
