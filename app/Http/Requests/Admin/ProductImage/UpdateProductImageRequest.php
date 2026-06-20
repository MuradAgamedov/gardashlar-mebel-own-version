<?php

namespace App\Http\Requests\Admin\ProductImage;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductImageRequest extends FormRequest
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
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp',
            'status' => 'boolean',
            'product_color_id' => 'nullable|exists:product_colors,id'
        ];
    }
}
