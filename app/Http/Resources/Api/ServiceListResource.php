<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceListResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $translation = $this->translates->first();

        return [
            'id' => $this->id,
            'slug' => $translation?->slug,
            'title' => $translation?->title ?? 'No title',
            'card_description' => $translation?->card_description ?? 'No description',
            'advantages_title' => $translation?->advantages_title,
        ];
    }
}
