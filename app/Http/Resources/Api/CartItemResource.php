<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $favoritesMap = $request->attributes->get('favoritesMap', collect());
        $compareIds   = $request->attributes->get('compareIds', []);

        $key = $this->product_id . '-' . $this->color_id;

        return [
            'cart_id'    => $this->id,
            'count'      => $this->count,
            'color_id'   => $this->color_id,
            'product'    => new CartProductResource($this->product),
            'inCart'     => [
                'isActive' => true,
                'color_id' => $this->color_id,
                'count'    => $this->count,
            ],
            'inFavorite' => [
                'isActive' => isset($favoritesMap[$key]),
                'color_id' => $this->color_id,
            ],
            'inCompare'  => in_array($this->product_id, $compareIds),
        ];
    }
}
