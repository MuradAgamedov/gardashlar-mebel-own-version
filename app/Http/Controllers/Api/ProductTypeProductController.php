<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductType;
use App\Models\HomePageTypeProduct;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use App\Traits\LocaleFromHeader;
use OpenApi\Annotations as OA;
use App\Models\ProductCategory;
class ProductTypeProductController extends Controller
{
    use LocaleFromHeader;

   
    public function index(Request $request, $productTypeId): JsonResponse
    {
        try {
            $languageId = $this->getLanguageIdFromHeader($request);
            $homepageOnly = $request->boolean('homepage_only', false);
    
            $productType = ProductType::where('id', $productTypeId)
                ->where('status', true)
                ->first();
                    
            if (!$productType) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product type not found'
                ], 404);
            }
    
            $cacheKey = "api:product-type:{$productTypeId}:products:lang:{$languageId}:homepage:" . ($homepageOnly ? 'true' : 'false');
    
            $products = Cache::rememberForever($cacheKey, function () use ($productTypeId, $languageId, $homepageOnly) {
                $query = Product::whereHas('types', function($q) use ($productTypeId) {
                    $q->where('product_types.id', $productTypeId);
                })->where('status', true);
                    
                // if ($homepageOnly) {
                //     $query->whereHas('homePageTypeProducts', function($q) use ($productTypeId) {
                //         $q->where('product_type_id', $productTypeId)
                //           ->where('status', true);
                //     });
                // }
                    
                return $query->with([
                    'translates' => function($q) use ($languageId) {
                        $q->where('language_id', $languageId);
                    },
                    'types.translates' => function($q) use ($languageId) {
                        $q->where('language_id', $languageId);
                    },
                    'images' => function($q) {
                        $q->active()->ordered();
                    },
                    'images.translates' => function($q) use ($languageId) {
                        $q->where('language_id', $languageId);
                    },
                    'categories.translates' => function($q) use ($languageId) {
                        $q->where('language_id', $languageId);
                    }
                ])
                ->orderBy('order')
                ->get();
            });
    
            // ✨ Bearer token yoxlaması
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
    
                $compareIds = \App\Models\Compare::where('user_id', $user->id)
                    ->whereIn('product_id', $productIds)
                    ->pluck('product_id')
                    ->toArray();
            }
    
            $products = $products->map(function ($product) use ($languageId, $cartIds, $favIds, $compareIds) {
                $translation = $product->translates->first();
                $mainImage = $product->images->where('is_main', true)->first() ?? $product->images->first();
                $mainImageAlt = '';
                if ($mainImage) {
                    $imgTr = $mainImage->translates->first();
                    $mainImageAlt = $imgTr ? $imgTr->alt_text : '';
                }
    
                $categorySlug = null;
                $parentSlug = null;
    
                if ($product->categories->isNotEmpty()) {
                    $category = $product->categories->first();
                    $categoryTranslation = $category->translates->first();
                    $categorySlug = $categoryTranslation ? $categoryTranslation->slug : null;
    
                    if ($category->parent_id) {
                        $parentCategory = ProductCategory::find($category->parent_id);
                        if ($parentCategory) {
                            $parentTranslation = $parentCategory->translates()->where('language_id', $languageId)->first();
                            $parentSlug = $parentTranslation ? $parentTranslation->slug : null;
                        }
                    }
                }
    
                return [
                    'id' => $product->id,
                    'title' => $translation ? $translation->title : 'No title',
                    'slug' => $translation ? $translation->slug : null,
                    'price' => $product->price,
                    'view_count' => $product->view_count ?? 0,
                    'card_image' => $mainImage && $mainImage->image ? asset('storage/' . $mainImage->image) : asset('images/default_image.webp'),
                    'card_image_alt' => $mainImageAlt,
                    'discount_price' => $product->discount_price,
                    'category_slug' => $categorySlug,
                    'parentSlug' => $parentSlug,
                    'product_types' => $product->types->map(function($type) {
                        $typeTranslation = $type->translates->first();
                        return [
                            'id' => $type->id,
                            'title' => $typeTranslation ? $typeTranslation->title : 'No title'
                        ];
                    }),
                    // ✅ Yeni boolean-lar
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
                'message' => 'Error retrieving products: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }
    
} 