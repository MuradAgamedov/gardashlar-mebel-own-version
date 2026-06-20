<?php

namespace App\Http\Requests\Admin\NewsImage;

use Illuminate\Foundation\Http\FormRequest;

class StoreNewsImageRequest extends FormRequest
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
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ];
    }
}
