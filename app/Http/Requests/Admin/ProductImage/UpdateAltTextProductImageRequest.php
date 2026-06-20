<?php

namespace App\Http\Requests\Admin\ProductImage;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAltTextProductImageRequest extends FormRequest
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
            'translates' => 'required|array',
            'translates.*.language_id' => 'required|exists:languages,id',
            'translates.*.alt_text' => 'nullable|string|max:255'
        ];
    }
}
