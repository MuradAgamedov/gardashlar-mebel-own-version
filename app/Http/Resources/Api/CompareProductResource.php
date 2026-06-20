<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompareProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $cartIds    = $request->attributes->get('cartIds', []);
        $favIds     = $request->attributes->get('favIds', []);
        $compareIds = $request->attributes->get('compareIds', []);
        $colorsMap  = $request->attributes->get('colorsMap', collect());

        $translation = $this->translates->first();
        $mainImage   = $this->images->where('is_main', true)->first() ?? $this->images->first();

        $details = $this->details->sortBy('order')->map(fn($d) => [
            'id'      => $d->id,
            'keyword' => $d->translates->first()?->keyword,
            'value'   => $d->translates->first()?->value,
        ])->values();

        $detailValues = $this->detailValues->sortBy('order')->map(fn($v) => [
            'id'      => $v->id,
            'keyword' => $v->translates->first()?->keyword,
            'details' => $v->details->sortBy('order')->map(fn($d) => [
                'id'      => $d->id,
                'keyword' => $d->translates->first()?->keyword,
                'value'   => $d->translates->first()?->value,
            ])->values(),
        ])->values();

        $productTypes = $this->types->map(fn($type) => [
            'id'    => $type->id,
            'title' => $type->translates->first()?->title ?? '',
            'slug'  => $type->translates->first()?->slug,
        ])->values();

        $colorIds        = $this->images->whereNotNull('product_color_id')->pluck('product_color_id')->unique();
        $availableColors = $colorIds->map(function ($id) use ($colorsMap) {
            $color = $colorsMap->get($id);
            return $color ? ['id' => $color->id, 'color_code' => $color->color_code] : null;
        })->filter()->values();

        $category           = $this->categories->first();
        $categorySlug       = $category?->translates->first()?->slug;
        $parentCategorySlug = $category?->parent?->translates->first()?->slug;

        return [
            'id'                  => $this->id,
            'title'               => $translation?->title ?? '',
            'slug'                => $translation?->slug ?? '',
            'price'               => $this->price,
            'discount_price'      => $this->discount_price,
            'card_image'          => $mainImage?->image ? asset('storage/' . $mainImage->image) : asset('images/default_image.webp'),
            'card_image_alt_text' => $mainImage?->image_alt_text,
            'details'             => $details,
            'detail_values'       => $detailValues,
            'product_types'       => $productTypes,
            'available_colors'    => $availableColors,
            'category_slug'       => $categorySlug,
            'parent_category_slug' => $parentCategorySlug,
            'inCart'              => in_array($this->id, $cartIds),
            'inFavorite'          => in_array($this->id, $favIds),
            'inCompare'           => in_array($this->id, $compareIds),
        ];
    }
}
