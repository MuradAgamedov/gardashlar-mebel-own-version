<?php

namespace App\Http\Requests\Api\Auth;

use App\Http\Requests\Api\ApiFormRequest;

class GoogleLoginRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'token' => 'required|string',
        ];
    }
}
