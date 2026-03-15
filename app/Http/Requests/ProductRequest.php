<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $id = $this->route('product') ? $this->route('product')->id : null;
        return [
            'category_id'    => 'nullable|exists:categories,id',
            'subcategory_id' => 'nullable|exists:subcategories,id',
            'brand_id'       => 'nullable|exists:brands,id',
            'name'           => 'required|string|max:255',
            'sku'            => "nullable|string|max:100|unique:products,sku,{$id}",
            'barcode'        => 'nullable|string|max:100',
            'purchase_price' => 'required|numeric|min:0',
            'mrp'            => 'required|numeric|min:0',
            'selling_price'  => 'required|numeric|min:0',
            // Must exist in the HSN master (type=hsn). tax_rate_id is auto-resolved
            // from this master record via the model's booted() hook — not a form field.
            'hsn_code'       => [
                'nullable',
                \Illuminate\Validation\Rule::exists('hsn_codes', 'code')
                    ->where('type', 'hsn')
                    ->where('is_active', true),
            ],
            'description'    => 'nullable|string',
            'status'         => 'in:active,inactive',
        ];
    }

    public function messages(): array
    {
        return [
            'hsn_code.exists' => 'The selected HSN code does not exist in the master list or is inactive.',
        ];
    }
}
