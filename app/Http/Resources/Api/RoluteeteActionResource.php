<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoluteeteActionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $translation = $this->translates->first();

        return [
            'id'      => $this->id,
            'percent' => $this->percent,
            'title'   => $translation?->title,
        ];
    }
}
