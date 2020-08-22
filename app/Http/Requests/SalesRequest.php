<?php

namespace App\Http\Requests;

use App\Distributor;
use App\Rules\ValidKey;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SalesRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $distributor = Distributor::where('slug', $this->distributor)->firstOrFail();

        return [
            'distributor' => ['required', 'string', 'exists:distributors,slug'],
            'keys' => ['required', 'array'],
            'keys.*' => [
                'string',
                'distinct',
                'alpha_dash',
                'min:16',
                'max:30',
                "unique:keys,serial_number,NULL,id,distributor_id,$distributor->id",
            ]
        ];
    }

    protected function prepareForValidation()
    {
        if (is_string($this->keys)) {
            $this->merge([
                'keys' => [$this->keys],
            ]);
        }
    }
}
