<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceAdvantageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $translation = $this->translates->first();

        return [
            'id' => $this->id,
            'icon' => $this->icon ? asset('storage/' . $this->icon) : '',
            'title' => $translation?->title ?? '',
            'icon_alt_text' => $translation?->icon_alt_text ?? '',
            'description' => $translation?->description ?? '',
        ];
    }
}
