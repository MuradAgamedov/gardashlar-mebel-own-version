<?php

namespace App\Http\Requests\Api\SimpleImage;

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
