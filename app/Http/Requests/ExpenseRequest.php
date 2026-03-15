<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExpenseRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'category_id' => 'nullable|exists:expense_categories,id',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string',
            'expense_date' => 'required|date',
            'payment_method' => 'nullable|in:cash,card,upi,bank_transfer',
        ];
    }
}
