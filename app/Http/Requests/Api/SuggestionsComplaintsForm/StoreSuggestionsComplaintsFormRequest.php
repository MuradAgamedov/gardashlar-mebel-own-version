<?php

namespace App\Http\Requests\Api\SuggestionsComplaintsForm;

use App\Http\Requests\Api\ApiFormRequest;

class StoreSuggestionsComplaintsFormRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'name'    => 'required|string|max:255',
            'mobile'  => ['required', 'string', 'max:20', 'regex:/^(?:\+\d{1,3}[-\s]?)?(?:\(?\d{2,4}\)?[-\s]?){2,4}\d{2,4}$/'],
            'email'   => 'required|email|max:255',
            'file'    => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,txt,jpg,jpeg,png|max:10240',
            'message' => 'required|string|max:2000',
        ];
    }
}
