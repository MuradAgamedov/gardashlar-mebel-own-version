<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TestimonialWithTranslationsResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $translations = $this->translates->map(fn($t) => [
            'language_code' => $t->language->lang_code,
            'language_name' => $t->language->title,
            'full_name'     => $t->full_name,
            'position'      => $t->position,
            'comment'       => $t->comment,
        ]);

        return [
            'id'               => $this->id,
            'customer_image_url' => $this->customer_image
                ? asset('storage/' . $this->customer_image)
                : asset('images/default_image.webp'),
            'company_logo_url' => $this->company_logo
                ? asset('storage/' . $this->company_logo)
                : asset('images/default_image.webp'),
            'translations' => $translations,
        ];
    }
}
