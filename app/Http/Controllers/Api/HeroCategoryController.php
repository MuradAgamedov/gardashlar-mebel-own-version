<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Interfaces\Services\Front\HeroCategoryServiceInterface;
use App\Interfaces\Services\Front\HeroProductServiceInterface;
use App\Http\Resources\Api\HeroCategoryResource;
use App\Http\Resources\Api\ProductCardResource;
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

            return $this->success(HeroCategoryResource::collection($heroCategories));

        } catch (\Exception $e) {
            return $this->serverError('Error retrieving hero categories: ' . $e->getMessage());
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

            $request->attributes->set('cartIds', $cartIds);
            $request->attributes->set('favIds', $favIds);
            $request->attributes->set('compareIds', $compareIds);

            $products = $heroProducts
                ->map(fn($hp) => $hp->product)
                ->filter()
                ->values();

            return $this->success(ProductCardResource::collection($products));

        } catch (\Exception $e) {
            return $this->serverError('Error retrieving hero products: ' . $e->getMessage());
        }
    }
}
