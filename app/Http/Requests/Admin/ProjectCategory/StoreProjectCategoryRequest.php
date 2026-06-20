<?php

namespace App\Http\Requests\Admin\ProjectCategory;

use Illuminate\Foundation\Http\FormRequest;

class StoreProjectCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {

        return [
            'status' => 'boolean',
            'translations' => 'required|array',
            'translations.*.language_id' => 'required|exists:languages,id',
            'translations.*.title' => 'required|string|max:255',
            'translations.*.slug' => 'required|string|max:255',
            'translations.*.seo_title' => 'nullable|string|max:255',
            'translations.*.seo_keywords' => 'nullable|string',
            'translations.*.seo_description' => 'nullable|string',
            'translations.*.seo_text' => 'nullable|string',
            'translations.*.image_alt_text' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ];
    }
}
