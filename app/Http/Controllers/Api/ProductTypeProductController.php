<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductType;
use App\Models\ProductCategory;
use App\Models\ProductCompare;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use App\Traits\LocaleFromHeader;

class ProductTypeProductController extends Controller
{
    use LocaleFromHeader;

    public function index(Request $request, $productTypeId): JsonResponse
    {
        try {
            $languageId = $this->getLanguageIdFromHeader($request);

            $productType = ProductType::where('id', $productTypeId)
                ->where('status', true)
                ->first();

            if (!$productType) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product type not found'
                ], 404);
            }

            $cacheKey = "api:product-type:{$productTypeId}:products:lang:{$languageId}";

            $products = Cache::rememberForever($cacheKey, function () use ($productTypeId, $languageId) {
                return Product::whereHas('types', function ($q) use ($productTypeId) {
                        $q->where('product_types.id', $productTypeId);
                    })
                    ->active()
                    ->with([
                        'translates' => fn ($q) => $q->where('language_id', $languageId),
                        'types.translates' => fn ($q) => $q->where('language_id', $languageId),
                        'images' => fn ($q) => $q->active()->ordered(),
                        'images.translates' => fn ($q) => $q->where('language_id', $languageId),
                        'categories.translates' => fn ($q) => $q->where('language_id', $languageId),
                    ])
                    ->orderBy('order')
                    ->get();
            });

            // 🔐 Auth user
            $user = auth('sanctum')->user();

            $cartIds = $favIds = $compareIds = [];

            if ($user && $products->isNotEmpty()) {
                $productIds = $products->pluck('id');

                $cartIds = \App\Models\Cart::where('user_id', $user->id)
                    ->whereIn('product_id', $productIds)
                    ->pluck('product_id')
                    ->toArray();

                $favIds = \App\Models\Favorite::where('user_id', $user->id)
                    ->whereIn('product_id', $productIds)
                    ->pluck('product_id')
                    ->toArray();

                // ✅ DÜZGÜN MODEL
                $compareIds = ProductCompare::where('user_id', $user->id)
                    ->whereIn('product_id', $productIds)
                    ->pluck('product_id')
                    ->toArray();
            }

            $products = $products->map(function ($product) use ($languageId, $cartIds, $favIds, $compareIds) {
                $translation = $product->translates->first();
                $mainImage = $product->images->where('is_main', true)->first() ?? $product->images->first();

                $categorySlug = null;
                $parentSlug = null;

                if ($product->categories->isNotEmpty()) {
                    $category = $product->categories->first();
                    $tr = $category->translates->first();
                    $categorySlug = $tr ? $tr->slug : null;

                    if ($category->parent_id) {
                        $parent = ProductCategory::find($category->parent_id);
                        if ($parent) {
                            $ptr = $parent->translates()->where('language_id', $languageId)->first();
                            $parentSlug = $ptr ? $ptr->slug : null;
                        }
                    }
                }

                return [
                    'id' => $product->id,
                    'title' => $translation ? $translation->title : '',
                    'slug' => $translation ? $translation->slug : '',
                    'price' => $product->price,
                    'discount_price' => $product->discount_price,
                    'view_count' => $product->view_count ?? 0,
                    'card_image' => $mainImage && $mainImage->image
                        ? asset('storage/' . $mainImage->image)
                        : asset('images/default_image.webp'),
                    'category_slug' => $categorySlug,
                    'parentSlug' => $parentSlug,

                    // ✅ BİRİNCİ CONTROLLER İLƏ TAM EYNİ
                    'inCart' => in_array($product->id, $cartIds),
                    'inFavorite' => in_array($product->id, $favIds),
                    'inCompare' => in_array($product->id, $compareIds),
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $products
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => []
            ], 500);
        }
    }
}
