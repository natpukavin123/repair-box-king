<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomerRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $id = $this->route('customer');
        return [
            'name' => 'required|string|max:150',
            'mobile_number' => "required|string|max:20|unique:customers,mobile_number,{$id}",
            'email' => 'nullable|email|max:150',
            'address' => 'nullable|string',
            'gstin' => 'nullable|string|max:15',
            'billing_state' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ];
    }
}
