<?php

namespace App\Http\Requests\Admin\CompanyManagement;

use Illuminate\Foundation\Http\FormRequest;

class UploadImageCompanyManagementRequest extends FormRequest
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
                'upload' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048'
            ];
    }
}
