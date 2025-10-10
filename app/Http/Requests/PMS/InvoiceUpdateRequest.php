<?php

namespace App\Http\Requests\PMS;

use Illuminate\Foundation\Http\FormRequest;

class InvoiceUpdateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'milestone_id' => 'nullable|exists:milestones,id',
            'invoice_date' => 'required|date',
               'invoice_type' => 'required',
            'due_date' => 'required|date|after:invoice_date',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ];
    }
}
