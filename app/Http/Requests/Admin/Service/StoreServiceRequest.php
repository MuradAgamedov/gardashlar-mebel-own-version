<?php

namespace App\Http\Requests\Admin\Service;

use Illuminate\Foundation\Http\FormRequest;

class StoreServiceRequest extends FormRequest
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
            'status' => 'nullable',
            'translates' => 'required|array|min:1',
            'translates.*.language_id' => 'required|exists:languages,id',
            'translates.*.title' => 'required|string|max:255',
            'translates.*.slug' => 'nullable|string|max:255',
            'translates.*.card_description' => 'required|string',
            'translates.*.main_description' => 'required|string',
            'translates.*.form_top_text' => 'nullable|string',
            'translates.*.advantages_title' => 'nullable|string|max:255',
            'translates.*.seo_title' => 'nullable|string|max:255',
            'translates.*.seo_keywords' => 'nullable|string',
            'translates.*.seo_description' => 'nullable|string',
        ], [
            'translates.required' => 'At least one language translation is required.',
            'translates.*.title.required' => 'Title is required for all languages.',
            'translates.*.card_description.required' => 'Card description is required for all languages.',
            'translates.*.main_description.required' => 'Main description is required for all languages.',
        ];
    }
}
