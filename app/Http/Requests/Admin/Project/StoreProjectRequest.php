<?php

namespace App\Http\Requests\Admin\Project;

use Illuminate\Foundation\Http\FormRequest;

class StoreProjectRequest extends FormRequest
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
            'project_category_id' => 'required|exists:project_categories,id',
            'status' => 'boolean',
            'translations' => 'required|array',
            'translations.*.language_id' => 'required|exists:languages,id',
            'translations.*.title' => 'required|string|max:255',
            'translations.*.company_name' => 'required|string|max:255',
            'translations.*.slug' => 'required|string|max:255|unique:project_translates,slug',
            'translations.*.description' => 'required|string',
            'translations.*.seo_title' => 'nullable|string|max:255',
            'translations.*.seo_keywords' => 'nullable|string',
            'translations.*.seo_description' => 'nullable|string',
        ];
    }
}
