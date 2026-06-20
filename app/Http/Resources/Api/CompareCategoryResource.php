<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompareCategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'title'      => $this->translates->first()?->title,
            'slug'       => $this->translates->first()?->slug,
            'parent_slug' => $this->parent?->translates->first()?->slug,
        ];
    }
}
