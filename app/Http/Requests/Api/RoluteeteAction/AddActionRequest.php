<?php

namespace App\Http\Requests\Api\RoluteeteAction;

use App\Http\Requests\Api\ApiFormRequest;

class AddActionRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'roluteete_action_id' => 'required|exists:roluteete_actions,id',
        ];
    }
}
