<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HeroCategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $productCategory = $this->productCategory;
        $translation = $productCategory->translates->first();

        return [
            'id' => $productCategory->id,
            'title' => $translation?->title,
        ];
    }
}
