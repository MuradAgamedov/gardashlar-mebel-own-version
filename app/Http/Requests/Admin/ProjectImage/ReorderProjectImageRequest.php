<?php

namespace App\Http\Requests\Admin\ProjectImage;

use Illuminate\Foundation\Http\FormRequest;

class ReorderProjectImageRequest extends FormRequest
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
                'items' => 'required|array|min:1',
                'items.*' => 'required|integer|exists:project_images,id'
            ];
    }
}
