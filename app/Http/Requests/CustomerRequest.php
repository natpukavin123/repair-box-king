<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CustomerRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => trim((string) $this->input('name', '')),
            'mobile_number' => substr(preg_replace('/\D+/', '', (string) $this->input('mobile_number', '')), 0, 10),
            'email' => trim((string) $this->input('email', '')),
            'address' => trim((string) $this->input('address', '')),
            'notes' => trim((string) $this->input('notes', '')),
        ]);
    }

    public function rules(): array
    {
        $customer = $this->route('customer');
        $id = is_object($customer) ? $customer->id : $customer;

        return [
            'name' => 'required|string|max:150',
            'mobile_number' => [
                'required',
                'regex:/^\d{10}$/',
                Rule::unique('customers', 'mobile_number')->ignore($id),
            ],
            'email' => 'nullable|email|max:150',
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Name is required.',
            'mobile_number.required' => 'Mobile number is required.',
            'mobile_number.regex' => 'Mobile must be exactly 10 digits.',
            'mobile_number.unique' => 'This mobile number is already linked to another customer.',
            'email.email' => 'Please enter a valid email address.',
        ];
    }
}
