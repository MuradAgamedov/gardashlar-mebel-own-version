<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompanyListResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $translation = $this->translates->first();

        return [
            'id' => $this->id,
            'slug' => $translation?->slug,
            'title' => $translation?->title,
            'card_image_url' => $translation?->card_image
                ? asset('storage/' . $translation->card_image)
                : null,
            'card_image_alt_text' => $translation?->card_image_alt_text,
        ];
    }
}
