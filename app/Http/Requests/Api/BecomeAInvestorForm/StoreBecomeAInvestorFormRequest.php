<?php

namespace App\Http\Requests\Api\BecomeAInvestorForm;

use App\Http\Requests\Api\ApiFormRequest;

class StoreBecomeAInvestorFormRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'name'    => 'required|string|max:255',
            'mobile'  => 'required|string|max:20',
            'email'   => 'required|email|max:255',
            'message' => 'required|string|max:1000',
            'file'    => 'nullable|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,txt',
        ];
    }
}
