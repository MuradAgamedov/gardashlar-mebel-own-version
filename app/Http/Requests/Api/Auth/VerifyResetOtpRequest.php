<?php

namespace App\Http\Requests\Api\Auth;

use App\Http\Requests\Api\ApiFormRequest;

class VerifyResetOtpRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'email'    => 'required|email',
            'otp_code' => 'required|string|size:6',
        ];
    }
}
