<?php

namespace App\Http\Requests\Api\Auth;

use App\Http\Requests\Api\ApiFormRequest;
use Illuminate\Contracts\Validation\Validator;

class VerifyOtpRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'phone'    => 'nullable|string|max:20',
            'email'    => 'nullable|email',
            'otp_code' => 'required|string|size:6',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v) {
            if (empty($this->phone) && empty($this->email)) {
                $v->errors()->add('contact_info', 'Telefon nömrəsi və ya email ünvanından ən azı biri məcburidir');
            }
        });
    }
}
