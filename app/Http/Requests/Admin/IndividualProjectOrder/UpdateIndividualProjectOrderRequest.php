<?php

namespace App\Http\Requests\Admin\IndividualProjectOrder;

use Illuminate\Foundation\Http\FormRequest;

class UpdateIndividualProjectOrderRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'translates' => 'required|array',
            'translates.*.language_id' => 'required|exists:languages,id',
            'translates.*.title' => 'required|string|max:255',
            'translates.*.description' => 'required|string',
            'status' => 'boolean'
        ];
    }
}
