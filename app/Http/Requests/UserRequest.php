<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $user = $this->route('user');
        $id = $user instanceof \App\Models\User ? $user->id : $user;
        $rules = [
            'name' => 'required|string|max:255',
            'email' => "required|email|max:255|unique:users,email,{$id}",
            'role_id' => 'nullable|exists:roles,id',
            'status' => 'in:active,inactive',
        ];
        if (!$id) {
            $rules['password'] = 'required|string|min:8|confirmed';
        } else {
            $rules['password'] = 'nullable|string|min:8|confirmed';
        }
        return $rules;
    }
}
