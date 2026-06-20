<?php

namespace App\Http\Requests\Admin\NewsCategory;

use Illuminate\Foundation\Http\FormRequest;

class ReorderNewsCategoryRequest extends FormRequest
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
            'categories' => 'required|array',
            'categories.*' => 'required|integer|exists:news_categories,id',
        ];
    }
}
