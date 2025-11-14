<?php

namespace App\Http\Requests\Admin\PopupAd;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePopupAdRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',

            // checkbox
            'status' => 'required|in:1,0',

            // translations array
            'translations' => 'required|array',

            // MÜTLƏQ!!! Əks halda language_id requestdən silinir
            'translations.*.language_id' => 'required|integer|exists:languages,id',

            'translations.*.locale' => 'required|string|max:5',
            'translations.*.title' => 'required|string|max:255',
            'translations.*.description' => 'required|string',

            'translations.*.image_alt_text' => 'nullable|string|max:255',

            // 🔥 YENİ əlavə etdiyimiz sahə (update üçün də lazımdır)
            'translations.*.link' => 'nullable|string|max:500',
        ];
    }
}
