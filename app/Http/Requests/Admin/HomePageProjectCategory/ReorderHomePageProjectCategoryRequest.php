<?php

namespace App\Http\Requests\Admin\HomePageProjectCategory;

use Illuminate\Foundation\Http\FormRequest;

class ReorderHomePageProjectCategoryRequest extends FormRequest
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
            'items' => 'required|array',
            'items.*.id' => 'required|exists:home_page_project_categories,id',
            'items.*.order' => 'required|integer|min:0',
        ];
    }
}
