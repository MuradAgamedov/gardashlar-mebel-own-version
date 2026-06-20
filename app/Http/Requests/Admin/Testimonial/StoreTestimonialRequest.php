<?php

namespace App\Http\Requests\Admin\Testimonial;

use Illuminate\Foundation\Http\FormRequest;

class StoreTestimonialRequest extends FormRequest
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
                "translations.{$language->id}.full_name" => 'required|string|max:255',
                "translations.{$language->id}.position" => 'required|string|max:255',
                "translations.{$language->id}.comment" => 'required|string|max:1000',
            ], [
                "translations.{$language->id}.full_name.required" => __('Full name is required for :language.', ['language' => $language->name]),
                "translations.{$language->id}.full_name.max" => __('Full name may not be greater than :max characters for :language.', ['max' => 255, 'language' => $language->name]),
                "translations.{$language->id}.position.required" => __('Position is required for :language.', ['language' => $language->name]),
                "translations.{$language->id}.position.max" => __('Position may not be greater than :max characters for :language.', ['max' => 255, 'language' => $language->name]),
                "translations.{$language->id}.comment.required" => __('Comment is required for :language.', ['language' => $language->name]),
                "translations.{$language->id}.comment.max" => __('Comment may not be greater than :max characters for :language.', ['max' => 1000, 'language' => $language->name]),
            ];
    }
}
