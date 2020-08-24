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
            'payment_session_identifier' => ['required', 'string', 'min:30', 'max:100']
        ];
    }
}
