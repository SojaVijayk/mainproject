<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMaintenanceRecordRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'asset_id' => 'required|exists:assets,id',
            'title' => 'required|string|max:255',
            'details' => 'required|string',
            'start_date' => 'required|date',
            'completion_date' => 'nullable|date|after_or_equal:start_date',
            'cost' => 'nullable|numeric|min:0',
            'status' => 'required|in:Scheduled,In Progress,Completed,Cancelled'
        ];
    }
}
