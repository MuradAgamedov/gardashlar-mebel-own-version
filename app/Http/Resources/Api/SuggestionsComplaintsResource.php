<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SuggestionsComplaintsResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $translation = $this->translates->first();

        return [
            'id'          => $this->id,
            'title'       => $translation?->title ?? '',
            'description' => $translation?->description ?? '',
        ];
    }
}
