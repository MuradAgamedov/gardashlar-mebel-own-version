<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $languageId = $request->attributes->get('languageId', 1);

        $category       = $this->categories->first();
        $parentCategory = $category?->parent;

        return [
            'id'             => $this->id,
            'title'          => $this->translates->where('language_id', $languageId)->first()?->title ?? '',
            'slug'           => $this->translates->where('language_id', $languageId)->first()?->slug ?? '',
            'price'          => $this->price,
            'discount_price' => $this->discount_price,
            'view_count'     => $this->view_count,

            'category_title' => $category?->translates->where('language_id', $languageId)->first()?->title ?? '',
            'category_slug'  => $category?->translates->where('language_id', $languageId)->first()?->slug ?? '',
            'parent_title'   => $parentCategory?->translates->where('language_id', $languageId)->first()?->title ?? '',
            'parent_slug'    => $parentCategory?->translates->where('language_id', $languageId)->first()?->slug ?? '',

            'images' => $this->images->sortBy('order')->map(fn($image) => [
                'id'        => $image->id,
                'image_url' => $image->image
                    ? asset('storage/' . $image->image)
                    : asset('images/default_image.webp'),
                'is_main'   => $image->is_main,
                'color_id'  => $image->product_color_id,
            ])->values(),

            'available_colors' => $this->colors->map(fn($color) => [
                'id'         => $color->id,
                'color_code' => $color->color_code,
            ]),

            'product_types' => $this->types->map(fn($type) => [
                'id'    => $type->id,
                'title' => $type->translates->where('language_id', $languageId)->first()?->title ?? '',
            ]),

            'product_details' => $this->details->map(fn($detail) => [
                'id'      => $detail->id,
                'keyword' => $detail->translates->where('language_id', $languageId)->first()?->keyword ?? '',
                'value'   => $detail->translates->where('language_id', $languageId)->first()?->value ?? '',
            ]),
        ];
    }
}
