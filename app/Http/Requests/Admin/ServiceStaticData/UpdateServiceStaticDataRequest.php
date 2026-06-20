<?php

namespace App\Http\Requests\Admin\ServiceStaticData;

use Illuminate\Foundation\Http\FormRequest;

class UpdateServiceStaticDataRequest extends FormRequest
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
                "translations.{$language->id}.title" => 'nullable|string|max:255',
                "translations.{$language->id}.description" => 'nullable|string|max:2000',
                "translations.{$language->id}.card_1_image_title" => 'nullable|string|max:255',
                "translations.{$language->id}.card_1_image_alt_text" => 'nullable|string|max:255',
                "translations.{$language->id}.card_2_image_title" => 'nullable|string|max:255',
                "translations.{$language->id}.card_2_image_alt_text" => 'nullable|string|max:255',
                "translations.{$language->id}.card_3_image_title" => 'nullable|string|max:255',
                "translations.{$language->id}.card_3_image_alt_text" => 'nullable|string|max:255',
            ], [
                "translations.{$language->id}.title.max" => "Title may not be greater than 255 characters for {$language->lang_code}.",
                "translations.{$language->id}.description.max" => "Description may not be greater than 2000 characters for {$language->lang_code}.",
                "translations.{$language->id}.card_1_image_title.max" => "Card 1 image title may not be greater than 255 characters for {$language->lang_code}.",
                "translations.{$language->id}.card_1_image_alt_text.max" => "Card 1 image alt text may not be greater than 255 characters for {$language->lang_code}.",
                "translations.{$language->id}.card_2_image_title.max" => "Card 2 image title may not be greater than 255 characters for {$language->lang_code}.",
                "translations.{$language->id}.card_2_image_alt_text.max" => "Card 2 image alt text may not be greater than 255 characters for {$language->lang_code}.",
                "translations.{$language->id}.card_3_image_title.max" => "Card 3 image title may not be greater than 255 characters for {$language->lang_code}.",
                "translations.{$language->id}.card_3_image_alt_text.max" => "Card 3 image alt text may not be greater than 255 characters for {$language->lang_code}.",
            ];
    }
}
