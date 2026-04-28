<?php

namespace App\Http\Requests\Admin\BlogPost;

use Illuminate\Foundation\Http\FormRequest;

class StoreBlogPostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'blog_category_id' => 'required|exists:blog_categories,id',
            'status' => 'boolean',
            'published_at' => 'nullable|date',
            'card_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp',
            'inner_banner_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp',
            'translations' => 'required|array',
            'translations.*.language_id' => 'required|exists:languages,id',
            'translations.*.title' => 'required|string|max:255',
            'translations.*.slug' => 'required|string|max:255|unique:blog_post_translates,slug',
            'translations.*.description' => 'required|string',
            'translations.*.card_image_alt_text' => 'nullable|string|max:255',
            'translations.*.card_description' => 'nullable|string',
            'translations.*.inner_banner_alt_text' => 'nullable|string|max:255',
            'translations.*.seo_title' => 'nullable|string|max:255',
            'translations.*.seo_keywords' => 'nullable|string',
            'translations.*.seo_description' => 'nullable|string',
        ];
    }
}
