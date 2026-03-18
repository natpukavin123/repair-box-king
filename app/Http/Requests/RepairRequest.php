<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RepairRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'customer_id' => 'required|exists:customers,id',
            'device_brand' => 'required|string|max:100',
            'device_model' => 'required|string|max:100',
            'imei' => 'nullable|string|max:50',
            'problem_description' => 'required|string',
            'estimated_cost' => 'nullable|numeric|min:0',
            'expected_delivery_date' => 'nullable|date',
            'technician_id' => 'nullable|exists:users,id',
        ];
    }
}
