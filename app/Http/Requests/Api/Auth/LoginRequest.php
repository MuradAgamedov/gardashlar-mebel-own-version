<?php

namespace App\Http\Requests\Api\Auth;

use App\Http\Requests\Api\ApiFormRequest;
use Illuminate\Contracts\Validation\Validator;

class LoginRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'email'    => 'nullable|email',
            'phone'    => 'nullable|string|max:20',
            'password' => 'required|string',
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
