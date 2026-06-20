<?php

namespace App\Http\Requests\Admin\Vacancy;

use Illuminate\Foundation\Http\FormRequest;

class UpdateVacancyRequest extends FormRequest
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
        'last_apply_date' => 'nullable|date',
        'email' => 'nullable|email|max:255',
        'phone' => 'nullable|max:50',
        'age' => 'nullable|integer|min:1|max:150',
        'status' => 'boolean',
    ];
    }
}
