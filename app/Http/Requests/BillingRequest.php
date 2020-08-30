<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use MarvinLabs\Luhn\Rules\LuhnRule;

class BillingRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'card' => ['required', 'integer', new LuhnRule()],
            'token' => ['required', 'string', 'min:30', 'max:100', 'exists:payments,token']
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'token' => $this->token
        ]);
    }
}
