<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceFormResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'service_id' => $this->service_id,
            'name' => $this->name,
            'mobile' => $this->mobile,
            'email' => $this->email,
            'message' => $this->message,
            'file' => $this->file,
            'created_at' => $this->created_at,
        ];
    }
}
