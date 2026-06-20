<?php

namespace App\Http\Requests\Api\ContactMessage;

use App\Http\Requests\Api\ApiFormRequest;

class StoreContactMessageRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'name'            => 'required|string|max:255',
            'email'           => 'required|email|max:255',
            'city'            => 'required|string|max:255',
            'message'         => 'required|string|max:5000',
            'recaptcha_token' => 'required|string',
        ];
    }
}
