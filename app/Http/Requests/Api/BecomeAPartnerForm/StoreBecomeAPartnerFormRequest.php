<?php

namespace App\Http\Requests\Api\BecomeAPartnerForm;

use App\Http\Requests\Api\ApiFormRequest;

class StoreBecomeAPartnerFormRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'name'    => 'required|string|max:255',
            'mobile'  => 'required|string|max:20',
            'email'   => 'required|email|max:255',
            'file'    => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,txt|max:10240',
            'message' => 'required|string|max:2000',
        ];
    }
}
