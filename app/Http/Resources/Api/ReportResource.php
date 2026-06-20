<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReportResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $translation = $this->translates->first();

        return [
            'id'          => $this->id,
            'pdf_url'     => $this->pdf_url,
            'title'       => $translation?->title,
            'description' => $translation?->description,
        ];
    }
}
