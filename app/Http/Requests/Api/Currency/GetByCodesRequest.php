<?php

namespace App\Http\Requests\Api\Currency;

use App\Http\Requests\Api\ApiFormRequest;

class GetByCodesRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'codes'   => 'required|array',
            'codes.*' => 'string|max:3',
        ];
    }
}
