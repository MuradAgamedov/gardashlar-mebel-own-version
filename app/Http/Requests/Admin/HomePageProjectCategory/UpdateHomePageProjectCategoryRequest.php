<?php

namespace App\Http\Requests\Admin\HomePageProjectCategory;

use Illuminate\Foundation\Http\FormRequest;

class UpdateHomePageProjectCategoryRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'project_category_id' => 'required|exists:project_categories,id|unique:home_page_project_categories,project_category_id,' . $this->route('home_page_project_category'),
            'status' => 'boolean',
        ];
    }
}
