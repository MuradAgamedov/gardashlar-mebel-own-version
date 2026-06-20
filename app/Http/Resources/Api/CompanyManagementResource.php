<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompanyManagementResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $translation = $this->translates->first();

        return [
            'id' => $this->id,
            'image_url' => $this->image ? asset('storage/' . $this->image) : asset('images/default_image.webp'),
            'title' => $translation ? $translation->title : '',
            'position' => $translation ? $translation->position : ''
        ];
    }
}
