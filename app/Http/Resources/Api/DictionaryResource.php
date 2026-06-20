<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DictionaryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $translation = $this->translates->first();

        return [
            'id'      => $this->id,
            'keyword' => $this->keyword,
            'value'   => $translation?->value ?? $this->keyword,
        ];
    }
}
