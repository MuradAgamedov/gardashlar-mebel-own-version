<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PopupAdResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $tr = $this->translations->first();

        return [
            'id' => $this->id,
            'image_url' => $this->image
                ? asset('storage/' . $this->image)
                : asset('images/default_image.webp'),
            'title' => $tr?->title ?? '',
            'description' => $tr?->description ?? '',
            'image_alt_text' => $tr?->image_alt_text ?? '',
            'link' => $tr?->link ?? '',
        ];
    }
}
