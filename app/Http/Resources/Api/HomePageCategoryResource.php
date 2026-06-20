<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HomePageCategoryResource extends JsonResource
{
    public int $languageId = 0;

    public function toArray(Request $request): array
    {
        $productCategory = $this->productCategory;
        $translation = $productCategory->translates->first();

        $parentSlug = null;
        if ($productCategory->parent_id !== null) {
            $parentSlug = $productCategory->parent?->translate($this->languageId)?->slug;
        }

        return [
            'id' => $productCategory->id,
            'image_url' => asset('storage/' . $productCategory->image),
            'title' => $translation?->title,
            'slug' => $translation?->slug,
            'parentSlug' => $parentSlug,
        ];
    }
}
