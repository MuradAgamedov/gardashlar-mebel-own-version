<?php

namespace App\Http\Requests\Admin\NewsPost;

use Illuminate\Foundation\Http\FormRequest;

class StoreNewsPostRequest extends FormRequest
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
            'news_category_id' => 'required|exists:news_categories,id',
            'status' => 'sometimes|boolean',
            'published_at' => 'nullable|date',
            'card_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'inner_banner_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ];

        // Add translation rules for each language
        foreach ($this->input('translations', []) as $languageId => $translation) {
            $rules["translations.{$languageId}.title"] = 'required|string|max:255';
            $rules["translations.{$languageId}.slug"] = 'required|string|max:255|unique:news_post_translates,slug,NULL,id,language_id,' . $languageId;
            $rules["translations.{$languageId}.card_description"] = 'nullable|string|max:500';
            $rules["translations.{$languageId}.description"] = 'nullable|string';
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
        $messages = [
            'news_category_id.required' => 'Please select a category.',
            'news_category_id.exists' => 'The selected category is invalid.',
            'card_image.image' => 'The card image must be an image file.',
            'card_image.mimes' => 'The card image must be a file of type: jpeg, png, jpg, gif, webp.',
            'card_image.max' => 'The card image may not be greater than 2MB.',
            'inner_banner_image.image' => 'The banner image must be an image file.',
            'inner_banner_image.mimes' => 'The banner image must be a file of type: jpeg, png, jpg, gif, webp.',
            'inner_banner_image.max' => 'The banner image may not be greater than 2MB.',
        ];

        foreach ($this->input('translations', []) as $languageId => $translation) {
            $messages["translations.{$languageId}.title.required"] = 'The title field is required.';
            $messages["translations.{$languageId}.slug.required"] = 'The slug field is required.';
            $messages["translations.{$languageId}.slug.unique"] = 'The slug has already been taken.';
        }

        return $messages;
    }
}
