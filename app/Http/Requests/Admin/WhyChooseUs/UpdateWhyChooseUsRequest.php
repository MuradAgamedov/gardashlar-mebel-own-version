<?php

namespace App\Http\Requests\Admin\WhyChooseUs;

use Illuminate\Foundation\Http\FormRequest;

class UpdateWhyChooseUsRequest extends FormRequest
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
            'first_icon' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
            'second_icon' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
            'third_icon' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
            'fourth_icon' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
            'translates' => 'required|array|min:1',
            'translates.*.language_id' => 'required|exists:languages,id',
            'translates.*.first_icon_alt_text' => 'nullable|string|max:255',
            'translates.*.first_title' => 'nullable|string|max:255',
            'translates.*.first_description' => 'nullable|string',
            'translates.*.second_icon_alt_text' => 'nullable|string|max:255',
            'translates.*.second_title' => 'nullable|string|max:255',
            'translates.*.second_description' => 'nullable|string',
            'translates.*.third_icon_alt_text' => 'nullable|string|max:255',
            'translates.*.third_title' => 'nullable|string|max:255',
            'translates.*.third_description' => 'nullable|string',
            'translates.*.fourth_icon_alt_text' => 'nullable|string|max:255',
            'translates.*.fourth_title' => 'nullable|string|max:255',
            'translates.*.fourth_description' => 'nullable|string',
        ], [
            'translates.required' => __('At least one language translation is required.'),
            'first_icon.image' => __('First icon must be an image file.'),
            'first_icon.mimes' => __('First icon must be a file of type: :types.', ['types' => 'jpg, jpeg, png, gif, webp']),
            'first_icon.max' => __('First icon may not be greater than :size.', ['size' => '2MB']),
            'second_icon.image' => __('Second icon must be an image file.'),
            'second_icon.mimes' => __('Second icon must be a file of type: :types.', ['types' => 'jpg, jpeg, png, gif, webp']),
            'second_icon.max' => __('Second icon may not be greater than :size.', ['size' => '2MB']),
            'third_icon.image' => __('Third icon must be an image file.'),
            'third_icon.mimes' => __('Third icon must be a file of type: :types.', ['types' => 'jpg, jpeg, png, gif, webp']),
            'third_icon.max' => __('Third icon may not be greater than :size.', ['size' => '2MB']),
            'fourth_icon.image' => __('Fourth icon must be an image file.'),
            'fourth_icon.mimes' => __('Fourth icon must be a file of type: :types.', ['types' => 'jpg, jpeg, png, gif, webp']),
            'fourth_icon.max' => __('Fourth icon may not be greater than :size.', ['size' => '2MB']),
        ];
    }
}
