<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HomePageProjectCategoryResource extends JsonResource
{
    public int $languageId = 0;

    public function toArray(Request $request): array
    {
        $projectCategory = $this->projectCategory;
        $translation = $projectCategory->translates->where('language_id', $this->languageId)->first()
            ?? $projectCategory->translates->first();

        return [
            'id' => $projectCategory->id,
            'title' => $translation?->title,
            'slug' => $translation?->slug,
            'image_url' => $projectCategory->image_url,
        ];
    }
}
