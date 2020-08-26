<?php

namespace App\Http\Requests;

use App\Distributor;
use Illuminate\Database\Query\Builder;
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
        return [
            'keys' => ['required', 'array'],
            'keys.*' => [
                'string',
                'distinct',
                'alpha_dash',
                'min:16',
                'max:30',
                "unique:keys,serial_number,NULL,id,distributor_id," . $this->distributor->id,
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
        $this->merge([
           'distributor' => Distributor::where('platform_id', $this->game->platform_id)->where('slug', $this->distributor)->firstOrFail(),
        ]);
    }
}
