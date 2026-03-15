<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ServiceRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'service_type_id' => 'nullable|exists:service_types,id',
            'customer_id' => 'nullable|exists:customers,id',
            'vendor_id' => 'nullable|exists:vendors,id',
            'description' => 'nullable|string',
            'vendor_cost' => 'nullable|numeric|min:0',
            'customer_charge' => 'required|numeric|min:0',
        ];
    }
}
