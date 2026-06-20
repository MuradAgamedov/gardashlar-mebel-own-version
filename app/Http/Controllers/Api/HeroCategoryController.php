<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Interfaces\Services\Front\HeroCategoryServiceInterface;
use App\Interfaces\Services\Front\HeroProductServiceInterface;
use App\Http\Resources\Api\HeroCategoryResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use App\Traits\LocaleFromHeader;

class HeroCategoryController extends Controller
{
    use LocaleFromHeader;

    protected HeroCategoryServiceInterface $heroCategoryService;
    protected HeroProductServiceInterface $heroProductService;

    public function __construct(
        HeroCategoryServiceInterface $heroCategoryService,
        HeroProductServiceInterface $heroProductService
    ) {
        $this->heroCategoryService = $heroCategoryService;
        $this->heroProductService = $heroProductService;
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $languageId = $this->getLanguageIdFromHeader($request);
            $cacheKey = "api:hero-categories:lang:{$languageId}";

            $heroCategories = Cache::rememberForever($cacheKey, function () use ($languageId) {
                return $this->heroCategoryService->getActiveOrderedWithTranslations($languageId);
            });

            return response()->json([
                'success' => true,
                'data' => HeroCategoryResource::collection($heroCategories),
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving hero categories: ' . $e->getMessage(),
                'data' => [],
            ], 500);
        }
    }

    public function getProductsByCategory(Request $request, int $categoryId): JsonResponse
    {
        try {
            $languageId = $this->getLanguageIdFromHeader($request);
            $cacheKey = "api:hero-products:category:{$categoryId}:lang:{$languageId}";

            $heroProducts = Cache::rememberForever($cacheKey, function () use ($categoryId, $languageId) {
                return $this->heroProductService->getActiveOrderedByCategory($categoryId, $languageId);
            });

            $user = auth('sanctum')->user();
            $cartIds = $favIds = $compareIds = [];

            if ($user && $heroProducts->isNotEmpty()) {
                $productIds = $heroProducts->pluck('product.id')->filter()->toArray();

                if (!empty($productIds)) {
                    $cartIds = \App\Models\Cart::where('user_id', $user->id)->whereIn('product_id', $productIds)->pluck('product_id')->toArray();
                    $favIds = \App\Models\Favorite::where('user_id', $user->id)->whereIn('product_id', $productIds)->pluck('product_id')->toArray();
                    $compareIds = \App\Models\Compare::where('user_id', $user->id)->whereIn('product_id', $productIds)->pluck('product_id')->toArray();
                }
            }

            $data = $heroProducts->map(function ($heroProduct) use ($cartIds, $favIds, $compareIds, $languageId) {
                $product = $heroProduct->product;
                if (!$product) {
                    return null;
                }
                $translate = $product->translates->first();
                $mainImage = $product->images->where('is_main', true)->first() ?? $product->images->first();

                $categorySlug = null;
                $parentSlug = null;

                if ($product->categories && $product->categories->isNotEmpty()) {
                    $category = $product->categories->first();
                    $categoryTranslation = $category->translates->first();
                    $categorySlug = $categoryTranslation?->slug;

                    if ($category->parent_id) {
                        $parentCategory = \App\Models\ProductCategory::find($category->parent_id);
                        if ($parentCategory) {
                            $parentTranslation = $parentCategory->translates()->where('language_id', $languageId)->first();
                            $parentSlug = $parentTranslation?->slug;
                        }
                    }
                }

                return [
                    'id' => $product->id,
                    'title' => $translate?->title,
                    'slug' => $translate?->slug,
                    'price' => $product->price,
                    'discount_price' => $product->discount_price ?? 0,
                    'main_image_url' => $mainImage?->image_url,
                    'category_slug' => $categorySlug,
                    'parentSlug' => $parentSlug,
                    'inCart' => in_array($product->id, $cartIds),
                    'inFavorite' => in_array($product->id, $favIds),
                    'inCompare' => in_array($product->id, $compareIds),
                ];
            })->filter()->values()->toArray();

            return response()->json(['success' => true, 'data' => $data], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving hero products: ' . $e->getMessage(),
                'data' => [],
            ], 500);
        }
    }
}
