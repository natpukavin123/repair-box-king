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
            'max_selling_price' => 'nullable|numeric|min:0',
            'description'    => 'nullable|string',
            'status'         => 'in:active,inactive',
            'opening_stock'  => 'nullable|integer|min:0',
        ];
    }
}
