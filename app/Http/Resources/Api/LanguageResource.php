<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LanguageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'lang_code' => $this->lang_code,
            'lang_name' => $this->lang_name,
            'is_default' => (bool) $this->is_default,
            'is_main_language' => (bool) $this->is_main_language,
            'order' => $this->order
        ];
    }
}
