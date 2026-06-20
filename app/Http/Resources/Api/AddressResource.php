<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AddressResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $typeTranslation = $this->addressType ? $this->addressType->translates->first() : null;
        $addressTranslation = $this->translates->first();

        return [
            'id' => $this->id,
            'type' => $typeTranslation ? $typeTranslation->title : '',
            'address' => $addressTranslation ? $addressTranslation->address : ''
        ];
    }
}
