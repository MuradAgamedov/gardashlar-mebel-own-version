<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ActionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $translation = $this->translates->first();

        return [
            'id' => $this->id,
            'company_id' => $this->company_id,
            'icon' => $this->icon,
            'status' => $this->status,
            'order' => $this->order,
            'start_date' => $this->start_date?->format('Y-m-d'),
            'end_date' => $this->end_date?->format('Y-m-d'),
            'title' => $translation?->title ?? '',
            'description' => $translation?->description,
            'image_url' => $translation?->image ? asset('storage/' . $translation->image) : null,
            'image_alt_text' => $translation?->image_alt_text,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
