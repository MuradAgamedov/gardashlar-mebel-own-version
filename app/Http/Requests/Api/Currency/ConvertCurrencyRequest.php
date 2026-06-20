<?php

namespace App\Http\Requests\Api\Currency;

use App\Http\Requests\Api\ApiFormRequest;

class ConvertCurrencyRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'amount' => 'required|numeric|min:0',
            'from'   => 'required|string|max:3',
            'to'     => 'required|string|max:3',
        ];
    }
}
