<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PurchasesRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'distributor' => [
                'required',
                'string',
                Rule::in($this->game->distributors->pluck('slug'))
            ],
        ];
    }
}
