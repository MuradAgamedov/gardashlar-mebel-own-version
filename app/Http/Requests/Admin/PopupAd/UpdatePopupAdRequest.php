<?php

namespace App\Http\Requests\Admin\PopupAd;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePopupAdRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // düzəldildi
    }

    public function rules(): array
    {
        return [
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'order' => 'nullable|integer|min:0',

            // Checkbox üçün ən stabil validation
            'status' => 'required|in:1,0',

            'translations' => 'required|array',

            'translations.*.locale' => 'required|string|max:5',
            'translations.*.title' => 'required|string|max:255',
            'translations.*.description' => 'required|string',
            'translations.*.image_alt_text' => 'nullable|string|max:255',
        ];
    }
}
