<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HeroCategory;
use App\Models\HeroProduct;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use App\Traits\LocaleFromHeader;
use OpenApi\Annotations as OA;

class HeroCategoryController extends Controller
{
    use LocaleFromHeader;

   
    public function index(Request $request): JsonResponse
    {
        try {
            $languageId = $this->getLanguageIdFromHeader($request);
            $cacheKey = "api:hero-categories:lang:{$languageId}";

            $heroCategories = Cache::rememberForever($cacheKey, function () use ($languageId) {
                return HeroCategory::active()
                    ->ordered()
                    ->with(['productCategory.translates' => fn($q) => $q->where('language_id', $languageId)])
                    ->get()
                    ->map(function ($heroCategory) {
                        $productCategory = $heroCategory->productCategory;
                        $translate = $productCategory->translates->first();
                        
                        return [
                            'id' => $productCategory->id,
                            'title' => $translate->title ?? null,
                        ];
                    });
            });

            return response()->json([
                'success' => true,
                'data' => $heroCategories
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving hero categories: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }

   
    public function getProductsByCategory(Request $request, int $categoryId): JsonResponse
    {
        try {
            $languageId = $this->getLanguageIdFromHeader($request);
            $cacheKey = "api:hero-products:category:{$categoryId}:lang:{$languageId}";
    
            $heroProducts = Cache::rememberForever($cacheKey, function () use ($categoryId, $languageId) {
                return HeroProduct::active()
                    ->ordered()
                    ->with([
                        'product.translates' => fn($q) => $q->where('language_id', $languageId),
                        'product.images' => fn($q) => $q->active()->ordered(),
                        'product.categories.translates' => fn($q) => $q->where('language_id', $languageId),
                    ])
                    ->get()
                    ->filter(function ($heroProduct) use ($categoryId) {
                        return $heroProduct->product->categories->contains('id', $categoryId);
                    });
            });
    
            // ✨ İstifadəçi məlumatı
            $user = auth('sanctum')->user();
            $cartIds = $favIds = $compareIds = [];
    
            if ($user && $heroProducts->isNotEmpty()) {
                $productIds = $heroProducts->pluck('product.id');
    
                $cartIds = \App\Models\Cart::where('user_id', $user->id)
                    ->whereIn('product_id', $productIds)
                    ->pluck('product_id')
                    ->toArray();
    
                $favIds = \App\Models\Favorite::where('user_id', $user->id)
                    ->whereIn('product_id', $productIds)
                    ->pluck('product_id')
                    ->toArray();
    
                $compareIds = \App\Models\Compare::where('user_id', $user->id)
                    ->whereIn('product_id', $productIds)
                    ->pluck('product_id')
                    ->toArray();
            }
    
            // ✨ Map nəticələri
            $heroProducts = $heroProducts->map(function ($heroProduct) use ($cartIds, $favIds, $compareIds, $languageId) {
                $product = $heroProduct->product;
                $translate = $product->translates->first();
    
                // Əsas şəkil
                $mainImage = $product->images->where('is_main', true)->first() ?? $product->images->first();
    
                // Kateqoriya slug-ları
                $categorySlug = null;
                $parentSlug = null;
    
                if ($product->categories->isNotEmpty()) {
                    $category = $product->categories->first();
                    $categoryTranslation = $category->translates->first();
                    $categorySlug = $categoryTranslation ? $categoryTranslation->slug : null;
    
                    if ($category->parent_id) {
                        $parentCategory = \App\Models\ProductCategory::find($category->parent_id);
                        if ($parentCategory) {
                            $parentTranslation = $parentCategory->translates()
                                ->where('language_id', $languageId)
                                ->first();
                            $parentSlug = $parentTranslation ? $parentTranslation->slug : null;
                        }
                    }
                }
    
                return [
                    'id' => $product->id,
                    'title' => $translate->title ?? null,
                    'slug' => $translate->slug ?? null,
                    'price' => $product->price ?? null,
                    'discount_price' => $product->discount_price ?? 0,
                    'main_image_url' => $mainImage ? $mainImage->image_url : null,
    
                    // ✅ Yeni sahələr
                    'category_slug' => $categorySlug,
                    'parentSlug' => $parentSlug,
    
                    // ✅ Yeni boolean-lar
                    'inCart' => in_array($product->id, $cartIds),
                    'inFavorite' => in_array($product->id, $favIds),
                    'inCompare' => in_array($product->id, $compareIds),
                ];
            })->values()->toArray();
    
            return response()->json([
                'success' => true,
                'data' => $heroProducts
            ], 200);
    
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving hero products: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }
    
    
} 