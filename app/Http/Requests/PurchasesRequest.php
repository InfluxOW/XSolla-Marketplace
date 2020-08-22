<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use MarvinLabs\Luhn\Rules\LuhnRule;

class PurchasesRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'distributor' => ['required', 'string', 'exists:distributors,slug'],
            'card' => ['required', 'integer', new LuhnRule()],
        ];
    }
}
