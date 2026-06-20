<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SuggestAPlaceFormResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name_surname' => $this->name_surname,
            'email' => $this->email,
            'area_sqm' => $this->area_sqm,
            'mobile' => $this->mobile,
            'address' => $this->address,
            'floor_count' => $this->floor_count,
            'is_repaired' => (bool)$this->is_repaired,
            'has_warehouse' => (bool)$this->has_warehouse,
            'has_parking' => (bool)$this->has_parking,
            'details' => $this->details,
            'file' => $this->file ? asset('storage/' . $this->file) : null,
            'created_at' => $this->created_at ? $this->created_at->toDateTimeString() : null,
        ];
    }
}
