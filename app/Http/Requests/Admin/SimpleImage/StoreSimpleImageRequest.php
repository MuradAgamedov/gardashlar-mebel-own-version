<?php

namespace App\Http\Requests\Admin\SimpleImage;

use Illuminate\Foundation\Http\FormRequest;

class StoreSimpleImageRequest extends FormRequest
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
            'keyword' => 'required|string|max:255|unique:simple_images',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp',
            'translations' => 'required|array',
            'translations.*.language_id' => 'required|exists:languages,id',
            'translations.*.image_go_url' => 'nullable|string|max:255',
            'translations.*.image_alt_text' => 'nullable|string|max:255',
        ], [
            'image.required' => 'Image is required.',
            'image.image' => 'The file must be an image.',
            'image.mimes' => 'Image must be a file of type: jpg, jpeg, png, gif, webp.',
        ];
    }
}
