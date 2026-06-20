<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BecomeAInvestorAdvantageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $translation = $this->translates->first();

        return [
            'id' => $this->id,
            'icon' => $this->icon ? asset('storage/' . $this->icon) : null,
            'title' => $translation ? $translation->title : '',
            'description' => $translation ? $translation->description : '',
            'icon_alt_text' => $translation ? $translation->icon_alt_text : ''
        ];
    }
}
