<?php

namespace App\Http\Requests\Admin\Vacancy;

use Illuminate\Foundation\Http\FormRequest;

class StoreVacancyRequest extends FormRequest
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
            "translations.{$language->id}.title" => 'max:255',
            "translations.{$language->id}.position" => 'max:255',
            "translations.{$language->id}.experience" => 'max:255',
            "translations.{$language->id}.work_schedule" => 'max:1000',
            "translations.{$language->id}.salary" => 'nullable|max:255',
            "translations.{$language->id}.address" => 'nullable|max:500',
            "translations.{$language->id}.education" => 'nullable|max:255',
            "translations.{$language->id}.related_person" => 'nullable|max:255',
            "translations.{$language->id}.city" => 'nullable|max:255',
            "translations.{$language->id}.seo_title" => 'nullable|max:255',
            "translations.{$language->id}.seo_keywords" => 'nullable|string',
            "translations.{$language->id}.seo_description" => 'nullable|string',
        ];
    }
}
