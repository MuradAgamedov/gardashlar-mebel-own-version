<?php

namespace App\Http\Requests\Api\VacancyForm;

use App\Http\Requests\Api\ApiFormRequest;

class StoreVacancyFormRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'vacancy_id' => 'required|integer|exists:vacancies,id',
            'name'       => 'required|string|max:255',
            'mobile'     => ['required', 'string', 'max:20', 'regex:/^(?:\+\d{1,3}[-\s]?)?(?:\(?\d{2,4}\)?[-\s]?){2,4}\d{2,4}$/'],
            'email'      => 'required|email|max:255',
            'message'    => 'required|string|max:1000',
            'file'       => 'nullable|file|max:10240|mimes:pdf,doc,docx',
        ];
    }
}
