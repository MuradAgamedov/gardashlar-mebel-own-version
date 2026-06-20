<?php

namespace App\Http\Requests\Api\Auth;

use App\Http\Requests\Api\ApiFormRequest;
use Illuminate\Contracts\Validation\Validator;

class RegisterRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'full_name'             => 'required|string|max:255',
            'phone'                 => 'nullable|string|max:20',
            'email'                 => 'nullable|email|unique:users,email',
            'password'              => 'required|string|min:6|confirmed',
            'password_confirmation' => 'required|string|min:6',
            'fin'                   => [
                'nullable',
                'regex:/^[A-NP-Z0-9]{7}$/',
                function ($attribute, $value, $fail) {
                    if ($value && \App\Models\User::where('fin', $value)->exists()) {
                        $fail(dict('fin_unique'));
                    }
                },
            ],
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
