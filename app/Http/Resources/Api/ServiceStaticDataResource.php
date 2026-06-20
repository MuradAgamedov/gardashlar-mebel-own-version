<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceStaticDataResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $translation = $this->translates->first();

        return [
            'id' => $this->id,
            'title' => $translation?->title,
            'description' => $translation?->description,
            'card_1_image_url' => $this->card_1_image ? asset('storage/' . $this->card_1_image) : null,
            'card_2_image_url' => $this->card_2_image ? asset('storage/' . $this->card_2_image) : null,
            'card_3_image_url' => $this->card_3_image ? asset('storage/' . $this->card_3_image) : null,
            'card_1_image_title' => $translation?->card_1_image_title,
            'card_1_image_alt_text' => $translation?->card_1_image_alt_text,
            'card_2_image_title' => $translation?->card_2_image_title,
            'card_2_image_alt_text' => $translation?->card_2_image_alt_text,
            'card_3_image_title' => $translation?->card_3_image_title,
            'card_3_image_alt_text' => $translation?->card_3_image_alt_text,
        ];
    }
}
