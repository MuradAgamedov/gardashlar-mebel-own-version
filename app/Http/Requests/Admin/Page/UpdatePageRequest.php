<?php

namespace App\Http\Requests\Admin\Page;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePageRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'status' => 'nullable',
            'keyword' => 'nullable|string|max:255',
            'translates' => 'required|array|min:1',
            'translates.*.language_id' => 'required|exists:languages,id',
            'translates.*.title' => 'required|string|max:255',
            'translates.*.slug' => 'required|string|max:255|unique:page_translates,slug,' . $this->route('page') . ',page_id',
            'translates.*.seo_title' => 'nullable|string|max:255',
            'translates.*.seo_keywords' => 'nullable|string',
            'translates.*.seo_description' => 'nullable|string',
        ];
    }
}
