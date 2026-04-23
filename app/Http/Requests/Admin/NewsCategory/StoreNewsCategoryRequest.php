<?php

namespace App\Http\Requests\Admin\NewsCategory;

use Illuminate\Foundation\Http\FormRequest;

class StoreNewsCategoryRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $rules = [
            'status' => 'sometimes|boolean',
        ];

        // Add translation rules for each language
        foreach ($this->input('translations', []) as $languageId => $translation) {
            $rules["translations.{$languageId}.title"] = 'required|string|max:255';
            $rules["translations.{$languageId}.slug"] = 'required|string|max:255|unique:news_category_translates,slug,NULL,id,language_id,' . $languageId;
            $rules["translations.{$languageId}.seo_title"] = 'nullable|string|max:255';
            $rules["translations.{$languageId}.seo_keywords"] = 'nullable|string|max:500';
            $rules["translations.{$languageId}.seo_description"] = 'nullable|string|max:1000';
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        $messages = [];

        foreach ($this->input('translations', []) as $languageId => $translation) {
            $messages["translations.{$languageId}.title.required"] = 'The title field is required.';
            $messages["translations.{$languageId}.slug.required"] = 'The slug field is required.';
            $messages["translations.{$languageId}.slug.unique"] = 'The slug has already been taken.';
        }

        return $messages;
    }
}
