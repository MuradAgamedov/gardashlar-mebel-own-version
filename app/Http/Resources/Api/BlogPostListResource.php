<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlogPostListResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $translation = $this->translates->first();
        $categoryTranslation = $this->category?->translates?->first();

        return [
            'id' => $this->id,
            'title' => $translation?->title,
            'slug' => $translation?->slug,
            'card_image' => $this->card_image,
            'card_image_url' => $this->card_image_url,
            'card_description' => $translation?->card_description,
            'category_slug' => $categoryTranslation?->slug,
        ];
    }
}
