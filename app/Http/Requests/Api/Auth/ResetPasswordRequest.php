<?php

namespace App\Http\Requests\Api\Auth;

use App\Http\Requests\Api\ApiFormRequest;
use Illuminate\Contracts\Validation\Validator;

class ResetPasswordRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'reset_token'           => 'required|string',
            'password'              => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required|string|min:8',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v) {
            $password = $this->password;
            if (!empty($password)) {
                if (!preg_match('/[!@#$%^&*()_+\-=\[\]{};\':"\\|,.<>\/?]/', $password)) {
                    $v->errors()->add('password', 'Şifrədə ən azı bir xüsusi simvol olmalıdır (!@#$%^&* və s.)');
                }
                if (!preg_match('/\d/', $password)) {
                    $v->errors()->add('password', 'Şifrədə ən azı bir rəqəm olmalıdır');
                }
                if (!preg_match('/[A-Z]/', $password)) {
                    $v->errors()->add('password', 'Şifrədə ən azı bir böyük hərf olmalıdır');
                }
            }
        });
    }
}
