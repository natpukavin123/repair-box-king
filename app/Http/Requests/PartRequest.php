<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
            'name' => 'required|string|max:150',
            'sku' => 'nullable|string|max:50|unique:parts,sku,' . $partId,
            'cost_price' => 'nullable|numeric|min:0',
            'selling_price' => 'nullable|numeric|min:0',
            'status' => 'sometimes|in:active,inactive',
        ];
    }
}
