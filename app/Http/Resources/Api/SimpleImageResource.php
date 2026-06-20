<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SimpleImageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $translation = $this->translates->first();

        return [
            'id'             => $this->id,
            'keyword'        => $this->keyword,
            'image_url'      => $this->image_url,
            'image_go_url'   => $translation?->image_go_url,
            'image_alt_text' => $translation?->image_alt_text,
        ];
    }
}
