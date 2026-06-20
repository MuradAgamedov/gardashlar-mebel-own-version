<?php

namespace App\Http\Requests\Api\Auth;

use App\Http\Requests\Api\ApiFormRequest;

class UpdateProfileRequest extends ApiFormRequest
{
    public function rules(): array
    {
        $user = auth()->user();

        return [
            'name'    => 'sometimes|required|string|max:255',
            'surname' => 'sometimes|required|string|max:255',
            'phone'   => 'sometimes|nullable|string|max:20',
            'email'   => [
                'sometimes',
                'nullable',
                'email',
                function ($attribute, $value, $fail) use ($user) {
                    if ($value && $value !== $user->email) {
                        $exists = \App\Models\User::where('email', $value)
                            ->where('id', '!=', $user->id)
                            ->exists();
                        if ($exists) {
                            $fail(dict('email_exists'));
                        }
                    }
                },
            ],
            'fin' => [
                'nullable',
                'string',
                function ($attribute, $value, $fail) use ($user) {
                    if ($value && $value !== $user->fin) {
                        $exists = \App\Models\User::where('fin', $value)
                            ->where('id', '!=', $user->id)
                            ->exists();
                        if ($exists) {
                            $fail(dict('fin_unique'));
                        }
                    }
                },
            ],
        ];
    }
}
