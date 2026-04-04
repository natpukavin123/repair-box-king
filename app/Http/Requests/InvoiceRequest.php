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
            'discount' => 'nullable|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.item_type' => 'required|in:product,service,recharge,manual,repair',
            'items.*.product_id' => 'nullable|exists:products,id',
            'items.*.service_id' => 'nullable|exists:service_types,id',
            'items.*.item_name' => 'required|string|max:255',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.mrp' => 'nullable|numeric|min:0',
            'items.*.is_linked' => 'nullable|boolean',
            'items.*.linked_id' => 'nullable|integer',
            // payments is optional – omit to create a draft/unpaid invoice
            'payments' => 'nullable|array',
            'payments.*.payment_method' => 'required_with:payments.*.amount|string|max:50',
            'payments.*.amount' => 'required_with:payments.*.payment_method|numeric|min:0',
            'payments.*.transaction_reference' => 'nullable|string|max:100',
        ];
    }
}
