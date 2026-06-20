<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BecomeAInvestorResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $translation = $this->translates->first();

        return [
            'id' => $this->id,
            'content' => $translation ? $translation->content : '',
            'advantages' => BecomeAInvestorAdvantageResource::collection($this->whenLoaded('advantages'))
        ];
    }
}
