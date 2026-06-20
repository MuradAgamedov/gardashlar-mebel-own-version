<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CatalogResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $translation = $this->translates->first();

        return [
            'id'           => $this->id,
            'catalog_year' => $translation?->catalog_year,
            'catalog_pdf'  => $this->catalog_pdf ? asset('storage/' . $this->catalog_pdf) : null,
            'pdf_filename' => $this->catalog_pdf ? basename($this->catalog_pdf) : null,
        ];
    }
}
