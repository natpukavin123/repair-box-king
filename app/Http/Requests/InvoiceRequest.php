<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InvoiceRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'customer_id' => 'nullable|exists:customers,id',
            'customer_billing_state' => 'nullable|string|max:100',
            'discount' => 'nullable|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.item_type' => 'required|in:product,service,recharge,manual',
            'items.*.product_id' => 'nullable|exists:products,id',
            'items.*.service_id' => 'nullable|exists:services,id',
            'items.*.item_name' => 'required|string|max:255',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            // GST overrides — if provided, skip auto-resolution
            'items.*.hsn_code' => 'nullable|string|max:10',
            'items.*.tax_rate_override' => 'nullable|numeric|min:0|max:100',
            'payments' => 'required|array|min:1',
            'payments.*.payment_method' => 'required|string|max:50',
            'payments.*.amount' => 'required|numeric|min:0',
            'payments.*.transaction_reference' => 'nullable|string|max:100',
        ];
    }
}
