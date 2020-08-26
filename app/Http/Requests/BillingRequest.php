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
            'payment_session_token' => ['required', 'string', 'min:30', 'max:100', 'exists:purchases,payment_session_token']
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'payment_session_token' => $this->payment_session_token
        ]);
    }
}
