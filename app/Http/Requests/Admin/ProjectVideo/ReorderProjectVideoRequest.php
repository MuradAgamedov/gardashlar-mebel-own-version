<?php

namespace App\Http\Requests\Admin\ProjectVideo;

use Illuminate\Foundation\Http\FormRequest;

class ReorderProjectVideoRequest extends FormRequest
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
            'items.*' => 'integer|exists:project_videos,id'
        ];
    }
}
