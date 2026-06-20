<?php

namespace App\Http\Requests\Admin\VacancyRequirement;

use Illuminate\Foundation\Http\FormRequest;

class ReorderVacancyRequirementRequest extends FormRequest
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
            'requirements' => 'required|array',
            'requirements.*' => 'required|integer|exists:vacancy_requirements,id',
        ];
    }
}
