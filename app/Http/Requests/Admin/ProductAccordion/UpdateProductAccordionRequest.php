<?php

namespace App\Http\Requests\Admin\ProductAccordion;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductAccordionRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'translations' => 'required|array',
            'translations.*.language_id' => 'required|exists:languages,id',
            'translations.*.title' => 'required|string|max:255',
            'translations.*.description' => 'nullable|string',
            'status' => 'boolean'
        ];
    }
}
