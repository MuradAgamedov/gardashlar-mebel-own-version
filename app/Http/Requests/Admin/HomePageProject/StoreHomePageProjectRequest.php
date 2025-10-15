<?php

namespace App\Http\Requests\Admin\HomePageProject;

use Illuminate\Foundation\Http\FormRequest;

class StoreHomePageProjectRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'project_id' => 'required|exists:projects,id|unique:home_page_projects,project_id',
            'project_category_id' => 'required|exists:project_categories,id',
            'status' => 'boolean',
        ];
    }
}
