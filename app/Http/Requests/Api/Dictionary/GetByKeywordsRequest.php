<?php

namespace App\Http\Requests\Api\Dictionary;

use App\Http\Requests\Api\ApiFormRequest;

class GetByKeywordsRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'keywords'   => 'required|array',
            'keywords.*' => 'string',
        ];
    }
}
