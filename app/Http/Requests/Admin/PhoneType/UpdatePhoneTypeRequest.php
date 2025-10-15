<?php

namespace App\Http\Requests\Admin\PhoneType;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePhoneTypeRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'translations.*.title' => 'required|string|max:255',
        ];
    }
}
