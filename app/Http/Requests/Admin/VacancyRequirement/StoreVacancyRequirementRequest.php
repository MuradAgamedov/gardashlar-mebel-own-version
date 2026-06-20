<?php

namespace App\Http\Requests\Admin\VacancyRequirement;

use Illuminate\Foundation\Http\FormRequest;

class StoreVacancyRequirementRequest extends FormRequest
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
                "translations.{$language->id}.title" => 'required|string|max:255',
                "translations.{$language->id}.description" => 'required|string',
            ], [
                "translations.{$language->id}.title.required" => "Title is required for {$language->lang_code}.",
                "translations.{$language->id}.title.max" => "Title may not be greater than 255 characters for {$language->lang_code}.",
                "translations.{$language->id}.description.required" => "Description is required for {$language->lang_code}.",
            ];
    }
}
