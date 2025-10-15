<?php

namespace App\Http\Requests\Admin\CustomerRights;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCustomerRightsRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'translations' => 'required|array',
            'translations.*.content' => 'required|string|max:5000',
        ];
    }
}
