<?php

namespace App\Http\Requests\Admin\Catalog;

use Illuminate\Foundation\Http\FormRequest;

class StoreCatalogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'catalog_pdf' => 'required|file|mimes:pdf',
            'status' => 'boolean',
            'translations' => 'required|array',
            'translations.*.catalog_year' => 'required|string|max:255',
        ];
    }
}
