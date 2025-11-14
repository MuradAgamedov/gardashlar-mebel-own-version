<?php

namespace App\Http\Requests\Admin\PopupAd;

use Illuminate\Foundation\Http\FormRequest;

class StorePopupAdRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',

            // Status checkbox
            'status' => 'required|in:1,0',

            // Translations
            'translations' => 'required|array',

            // LANGUAGE ID MÜTLƏQ OLMALIDIR!
            'translations.*.language_id' => 'required|exists:languages,id',

            'translations.*.locale' => 'required|string|max:5',
            'translations.*.title' => 'required|string|max:255',
            'translations.*.description' => 'required|string',
            'translations.*.image_alt_text' => 'nullable|string|max:255',
        ];
    }
}
