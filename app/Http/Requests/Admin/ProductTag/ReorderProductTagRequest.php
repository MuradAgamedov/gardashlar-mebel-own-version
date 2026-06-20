<?php

namespace App\Http\Requests\Admin\ProductTag;

use Illuminate\Foundation\Http\FormRequest;

class ReorderProductTagRequest extends FormRequest
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
            'items.*' => 'required|integer|exists:product_tags,id'
        ];
    }
}
