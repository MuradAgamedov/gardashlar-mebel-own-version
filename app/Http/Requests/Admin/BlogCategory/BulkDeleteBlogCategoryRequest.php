<?php

namespace App\Http\Requests\Admin\BlogCategory;

use Illuminate\Foundation\Http\FormRequest;

class BulkDeleteBlogCategoryRequest extends FormRequest
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
            'ids' => 'required|array',
            'ids.*' => 'required|integer|exists:blog_categories,id',
        ];
    }
}
