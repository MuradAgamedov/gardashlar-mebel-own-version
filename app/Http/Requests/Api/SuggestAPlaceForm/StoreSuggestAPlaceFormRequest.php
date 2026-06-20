<?php

namespace App\Http\Requests\Api\SuggestAPlaceForm;

use App\Http\Requests\Api\ApiFormRequest;

class StoreSuggestAPlaceFormRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'name_surname'  => 'required|string|max:255',
            'email'         => 'required|email|max:255',
            'area_sqm'      => 'required|string|max:50',
            'mobile'        => ['required', 'string', 'max:20', 'regex:/^(?:\+\d{1,3}[-\s]?)?(?:\(?\d{2,4}\)?[-\s]?){2,4}\d{2,4}$/'],
            'address'       => 'required|string|max:500',
            'floor_count'   => 'required|string|max:50',
            'is_repaired'   => 'required|boolean',
            'has_warehouse' => 'required|boolean',
            'has_parking'   => 'required|boolean',
            'details'       => 'required|string|max:2000',
            'file'          => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,txt,jpg,jpeg,png|max:10240',
        ];
    }
}
