<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RechargeRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'customer_id'     => 'nullable|exists:customers,id',
            'provider_id'     => 'required|exists:recharge_providers,id',
            'mobile_number'   => 'required|digits:10',
            'plan_name'       => 'nullable|string|max:150',
            'recharge_amount' => 'required|integer|min:1',
            'payment_method'  => 'required|string|max:50',
            'transaction_id'  => 'nullable|string|max:100',
        ];
    }
}
