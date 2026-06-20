<?php

namespace App\Http\Requests\Api\Auth;

use App\Http\Requests\Api\ApiFormRequest;

class ConfirmEmailChangeRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'otp_code' => 'required|string|size:6',
        ];
    }
}
