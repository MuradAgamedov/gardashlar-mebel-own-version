<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WhyChooseUsResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $translation = $this->translates->first();

        return [
            'id' => $this->id,
            'first_icon_url' => $this->first_icon_url,
            'second_icon_url' => $this->second_icon_url,
            'third_icon_url' => $this->third_icon_url,
            'fourth_icon_url' => $this->fourth_icon_url,
            'first_title' => $translation ? $translation->first_title : null,
            'first_description' => $translation ? $translation->first_description : null,
            'second_title' => $translation ? $translation->second_title : null,
            'second_description' => $translation ? $translation->second_description : null,
            'third_title' => $translation ? $translation->third_title : null,
            'third_description' => $translation ? $translation->third_description : null,
            'fourth_title' => $translation ? $translation->fourth_title : null,
            'fourth_description' => $translation ? $translation->fourth_description : null
        ];
    }
}
