<?php

namespace App\Http\Requests\Admin\SeoPage;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSeoPageRequest extends FormRequest
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
        $id = $this->route('id');

        return [
            'category_id' => 'nullable|exists:product_categories,id',
            'status' => 'nullable|boolean',
            'translates' => 'required|array',
            'translates.*.language_id' => 'required|exists:languages,id',
            // Hər dil üçün məcburi deyil
            'translates.*.title' => 'nullable|string',
            'translates.*.slug' => 'nullable|string|max:255|unique:seo_page_translates,slug,' . $id . ',seo_page_id',
            'translates.*.page_footer' => 'nullable|string',
            'translates.*.meta_title' => 'nullable|string',
            'translates.*.meta_description' => 'nullable|string',
            'translates.*.meta_keywords' => 'nullable|string',
        ];
    }
}
