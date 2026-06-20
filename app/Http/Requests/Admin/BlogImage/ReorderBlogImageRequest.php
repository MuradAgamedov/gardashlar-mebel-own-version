<?php

namespace App\Http\Requests\Admin\BlogImage;

use Illuminate\Foundation\Http\FormRequest;

class ReorderBlogImageRequest extends FormRequest
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
            'images' => 'required|array',
            'images.*.id' => 'required|integer|exists:blog_images,id',
            'images.*.order' => 'required|integer|min:0',
        ];
    }
}
