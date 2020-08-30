<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255', 'min:3'],
            'username' => ['required', 'string', 'max:255', 'min:3', 'alpha_dash', 'unique:users'],
            'role' => ['required', 'string', 'in:seller,buyer'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }

    public function messages()
    {
        return [
            'role.in' => 'Available roles are buyer and seller.',
        ];
    }
}
