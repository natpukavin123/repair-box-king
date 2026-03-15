<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BrandRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $id = $this->route('brand');
        return [
            'name' => "required|string|max:150|unique:brands,name,{$id}",
            'status' => 'in:active,inactive',
        ];
    }
}
