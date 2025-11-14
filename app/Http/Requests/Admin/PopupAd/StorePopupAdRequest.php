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

            // checkbox validation
            'status' => 'required|in:1,0',

            // translations array
            'translations' => 'required|array',

            // each translation must contain language_id + locale
            'translations.*.language_id' => 'required|integer|exists:languages,id',
            'translations.*.locale' => 'required|string|max:5',

            // text fields
            'translations.*.title' => 'required|string|max:255',
            'translations.*.description' => 'required|string',
            'translations.*.image_alt_text' => 'nullable|string|max:255',
        ];
    }
}
