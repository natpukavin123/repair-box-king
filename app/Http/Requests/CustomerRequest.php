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
            'mobile_number' => "required|regex:/^\d{10}$/|unique:customers,mobile_number,{$id}",
            'email' => 'nullable|email|max:150',
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
        ];
    }
}
