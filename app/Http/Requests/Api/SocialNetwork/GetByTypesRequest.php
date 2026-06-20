<?php

namespace App\Http\Requests\Api\SocialNetwork;

use App\Http\Requests\Api\ApiFormRequest;

class GetByTypesRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'types'   => 'required|array',
            'types.*' => 'string',
        ];
    }
}
