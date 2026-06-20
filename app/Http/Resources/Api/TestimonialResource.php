<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TestimonialResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $translation = $this->translates->first();

        return [
            'id' => $this->id,
            'customer_image_url' => $this->customer_image
                ? asset('storage/' . $this->customer_image)
                : asset('images/default_image.webp'),
            'company_logo_url' => $this->company_logo
                ? asset('storage/' . $this->company_logo)
                : asset('images/default_image.webp'),
            'full_name' => $translation?->full_name,
            'position' => $translation?->position,
            'comment' => $translation?->comment,
        ];
    }
}
