<?php

namespace App\Http\Requests\Admin\WhyChooseUsSlider;

use Illuminate\Foundation\Http\FormRequest;

class StoreWhyChooseUsSliderRequest extends FormRequest
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
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
            'video_link' => 'nullable|url|max:500',
            'status' => 'boolean',
        ], [
            'image.image' => __('Image must be an image file.'),
            'image.mimes' => __('Image must be a file of type: :types.', ['types' => 'jpg, jpeg, png, gif, webp']),
            'image.max' => __('Image may not be greater than :size.', ['size' => '2MB']),
            'video_link.url' => __('Video link must be a valid URL.'),
            'status.boolean' => __('Status must be true or false.'),
        ];
    }
}
