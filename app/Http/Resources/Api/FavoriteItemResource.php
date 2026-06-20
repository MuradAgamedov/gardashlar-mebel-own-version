<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FavoriteItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $cartItems  = $request->attributes->get('cartItems', collect());
        $compareIds = $request->attributes->get('compareIds', []);

        $cartKey = $this->product_id . '-' . $this->color_id;
        $inCart  = $cartItems[$cartKey] ?? null;

        return [
            'favorite_id' => $this->id,
            'color_id'    => $this->color_id,
            'product'     => new CartProductResource($this->product),
            'inFavorite'  => [
                'isActive' => true,
                'color_id' => $this->color_id,
            ],
            'inCart'      => [
                'isActive' => $inCart ? true : false,
                'color_id' => $this->color_id,
                'count'    => $inCart?->count ?? 0,
            ],
            'inCompare'   => in_array($this->product_id, $compareIds),
        ];
    }
}
