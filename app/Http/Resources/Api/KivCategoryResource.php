<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class KivCategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $translation = $this->translates->first();

        return [
            'id'    => $this->id,
            'title' => $translation?->title ?? '',
            'kivs'  => KivResource::collection($this->whenLoaded('kivs')),
        ];
    }
}
