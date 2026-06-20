<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HomePageDiscountResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $translation = $this->translates->first();

        $bgImageUrl = null;
        if ($this->bg_image) {
            $imageService = app(\App\Services\ImageService::class);
            $bgImageUrl = $imageService->getUrl($this->bg_image);
        }

        return [
            'id' => $this->id,
            'bg_image_url' => $bgImageUrl,
            'title' => $translation?->title,
            'subtitle' => $translation?->subtitle,
            'button_link' => $translation?->button_link,
        ];
    }
}
