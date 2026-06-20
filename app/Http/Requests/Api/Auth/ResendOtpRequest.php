<?php

namespace App\Http\Requests\Api\Auth;

use App\Http\Requests\Api\ApiFormRequest;
use Illuminate\Contracts\Validation\Validator;

class ResendOtpRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'phone'   => 'nullable|string|max:20',
            'email'   => 'nullable|email',
            'purpose' => 'nullable|string|in:registration,password_reset',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v) {
            if (empty($this->phone) && empty($this->email)) {
                $v->errors()->add('contact_info', dict('contact_info'));
            }
        });
    }
}
