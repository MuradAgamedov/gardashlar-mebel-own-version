<?php

namespace App\Http\Requests\Admin\Company;

use Illuminate\Foundation\Http\FormRequest;

class StoreCompanyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'translates' => 'required|array',
            'translates.*.language_id' => 'required|exists:languages,id',
            'translates.*.title' => 'required|string|max:255',
            'translates.*.slug' => 'nullable|string|max:255|unique:company_translates,slug',
            'translates.*.card_image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'translates.*.card_image_alt_text' => 'nullable|string|max:255',
            'translates.*.inner_image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'translates.*.inner_image_alt_text' => 'nullable|string|max:255',
            'translates.*.description' => 'required|string',
            'status' => 'boolean'
        ];
    }
}
