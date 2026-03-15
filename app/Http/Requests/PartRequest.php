<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PartRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $partId = $this->route('part')?->id;

        return [
            'name'          => 'required|string|max:150',
            'sku'           => 'nullable|string|max:50|unique:parts,sku,' . $partId,
            'cost_price'    => 'nullable|numeric|min:0',
            'selling_price' => 'nullable|numeric|min:0',
            // Must exist in the HSN master (type=hsn). tax_rate_id is auto-resolved
            // from this master record via the model's booted() hook — not a form field.
            'hsn_code'      => [
                'nullable',
                Rule::exists('hsn_codes', 'code')
                    ->where('type', 'hsn')
                    ->where('is_active', true),
            ],
            'status'        => 'sometimes|in:active,inactive',
        ];
    }

    public function messages(): array
    {
        return [
            'hsn_code.exists' => 'The selected HSN code does not exist in the master list or is inactive.',
        ];
    }
}
