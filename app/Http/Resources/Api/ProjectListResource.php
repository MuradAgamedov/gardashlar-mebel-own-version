<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectListResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $translation = $this->translates->first();
        $categoryTranslation = $this->category?->translates?->first();
        $mainImage = $this->images->first();

        return [
            'id' => $this->id,
            'title' => $translation?->title,
            'slug' => $translation?->slug,
            'company_name' => $translation?->company_name,
            'category_slug' => $categoryTranslation?->slug,
            'main_image' => $mainImage?->image_url ?? asset('images/default_image.webp'),
        ];
    }
}
