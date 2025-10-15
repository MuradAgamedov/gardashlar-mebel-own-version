<?php

namespace App\Http\Requests\Admin\KivCategory;

use Illuminate\Foundation\Http\FormRequest;

class StoreKivCategoryRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'status' => 'boolean',
            'translations.*.title' => 'required|string|max:255',
            'translations.*.subtitle' => 'nullable|string|max:255',
        ];
    }
}
