<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PhoneTypeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $translation = $this->translates->first();

        return [
            'type'   => $translation?->title ?? '',
            'phones' => $this->phones->map(fn($phone) => [
                'id'    => $phone->id,
                'phone' => $phone->phone,
            ])->values(),
        ];
    }
}
