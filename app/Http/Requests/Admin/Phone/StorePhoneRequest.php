<?php

namespace App\Http\Requests\Admin\Phone;

use Illuminate\Foundation\Http\FormRequest;

class StorePhoneRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'phone_type_id' => 'required|exists:phone_types,id',
            'phone' => 'required|string|max:255',
        ];
    }
}
