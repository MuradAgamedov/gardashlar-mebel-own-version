<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
class ProductCardResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $translation = $this->translates->first();
        $mainImage   = $this->images->where('is_main', true)->first() ?? $this->images->first();

        $categorySlug = null;
        $parentSlug   = null;

        if ($this->categories && $this->categories->isNotEmpty()) {
            $category     = $this->categories->first();
            $categorySlug = $category->translates->first()?->slug;

            if ($category->parent_id && $category->relationLoaded('parent')) {
                $parentSlug = $category->parent?->translates->first()?->slug;
            }
        }

        $cardImageAlt = null;
        if ($mainImage && $mainImage->relationLoaded('translates')) {
            $cardImageAlt = $mainImage->translates->first()?->alt_text;
        }

        $cartIds    = $request->attributes->get('cartIds', []);
        $favIds     = $request->attributes->get('favIds', []);
        $compareIds = $request->attributes->get('compareIds', []);

        $productTypes = $this->whenLoaded('types', fn() =>
            $this->types->map(fn($t) => [
                'id'    => $t->id,
                'title' => $t->translates->first()?->title ?? '',
            ])
        );

        return [
            'id'              => $this->id,
            'title'           => $translation?->title ?? '',
            'slug'            => $translation?->slug ?? '',
            'price'           => $this->price,
            'discount_price'  => $this->discount_price,
            'view_count'      => $this->view_count ?? 0,
            'card_image'      => $mainImage?->image
                ? asset('storage/' . $mainImage->image)
                : asset('images/default_image.webp'),
            'card_image_alt'  => $cardImageAlt,
            'category_slug'   => $categorySlug,
            'parentSlug'      => $parentSlug,
            'product_types'   => $productTypes ?? [],
            'inCart'          => in_array($this->id, $cartIds),
            'inFavorite'      => in_array($this->id, $favIds),
            'inCompare'       => in_array($this->id, $compareIds),
        ];
    }
}
