<?php

namespace App\Http\Requests\Api\Subscriber;

use App\Http\Requests\Api\ApiFormRequest;

class StoreSubscriberRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'email' => 'required|email|unique:subscribers,email|max:255',
        ];
    }
}
