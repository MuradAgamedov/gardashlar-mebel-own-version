<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SuggestionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $translation = $this->translates->first();
        return [
            'id' => $this->id,
            'title' => $translation ? $translation->title : '',
            'image_alt_text' => $translation ? $translation->image_alt_text : '',
            'image' => $this->image ? asset('storage/' . $this->image) : asset('images/default_image.webp'),
            'pdf' => $this->pdf ? asset('storage/' . $this->pdf) : null,
            'has_pdf' => !empty($this->pdf),
            'order' => $this->order
        ];
    }
}
