<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Interfaces\Services\Front\SeoPageServiceInterface;
use App\Interfaces\Services\Front\ProductServiceInterface;
use App\Http\Resources\Api\SeoPageResource;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use App\Traits\LocaleFromHeader;

class SeoPageController extends Controller
{
    use LocaleFromHeader;

    protected SeoPageServiceInterface $seoPageService;
    protected ProductServiceInterface $productService;

    public function __construct(
        SeoPageServiceInterface $seoPageService,
        ProductServiceInterface $productService
    ) {
        $this->seoPageService = $seoPageService;
        $this->productService = $productService;
    }

    /**
     * Get all SEO pages with title and slug only
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $languageId = $this->getLanguageIdFromHeader($request);
            $cacheKey = "api_seo_pages_{$languageId}";
            
            $seoPages = Cache::rememberForever($cacheKey, function () use ($languageId) {
                return $this->seoPageService->getActiveOrderedWithTranslations($languageId);
            });

            $filtered = $seoPages->filter(function ($page) {
                $tr = $page->translates->first();
                return !empty($tr?->title) && !empty($tr?->slug);
            })->values();

            return response()->json([
                'success' => true,
                'data' => SeoPageResource::collection($filtered)
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching SEO pages: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }

    /**
     * Get specific SEO page by slug
     */
    public function show(Request $request, $slug): JsonResponse
    {
        try {
            $languageId = $this->getLanguageIdFromHeader($request);
            $cacheKey = "api_seo_page_{$slug}_{$languageId}";
            
            $seoPageData = Cache::rememberForever($cacheKey, function () use ($slug, $languageId) {
                $seoPageTranslate = $this->seoPageService->getTranslateBySlug($slug, $languageId);

                if (!$seoPageTranslate || !$seoPageTranslate->seoPage || !$seoPageTranslate->seoPage->status) {
                    return null;
                }

                $categoryData = null;
                if ($seoPageTranslate->seoPage->category) {
                    $categoryTranslate = $seoPageTranslate->seoPage->category
                        ->translates()
                        ->where('language_id', $languageId)
                        ->first();
                    
                    if ($categoryTranslate) {
                        $categoryData = [
                            'id' => $seoPageTranslate->seoPage->category->id,
                            'title' => $categoryTranslate->title,
                            'slug' => $categoryTranslate->slug ?? null
                        ];
                    }
                }

                $products = [];
                if ($seoPageTranslate->seoPage->category_id) {
                    $categoryId = $seoPageTranslate->seoPage->category_id;
                    
                    $category = ProductCategory::find($categoryId);
                    $categoryIds = [$categoryId];
                    
                    if ($category) {
                        $children = ProductCategory::where('parent_id', $categoryId)->pluck('id');
                        if ($children->isNotEmpty()) {
                            $categoryIds = array_merge($categoryIds, $children->toArray());
                        }
                    }
                    
                    $productsCollection = $this->productService->getProductsByCategories($categoryIds, $languageId, 12);
                    
                    $products = $productsCollection->map(function ($product) use ($languageId) {
                        $translation = $product->translates->first();
                        $mainImage = $product->images->where('is_main', true)->first() ?? $product->images->first();
                        
                        $categorySlug = null;
                        $parentSlug = null;
                        if ($product->categories && $product->categories->isNotEmpty()) {
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
                        
                        $productTypes = $product->types->map(function ($type) {
                            $translation = $type->translates->first();
                            return [
                                'id' => $type->id,
                                'title' => $translation ? $translation->title : ''
                            ];
                        });
                        
                        $user = auth('sanctum')->user();
                        $inCart = $user ? \App\Models\Cart::where('user_id', $user->id)->where('product_id', $product->id)->exists() : false;
                        $inFavorite = $user ? \App\Models\Favorite::where('user_id', $user->id)->where('product_id', $product->id)->exists() : false;
                        $inCompare = $user ? \App\Models\ProductCompare::where('user_id', $user->id)->where('product_id', $product->id)->exists() : false;
                        
                        return [
                            'id' => $product->id,
                            'title' => $translation ? $translation->title : '',
                            'slug' => $translation ? $translation->slug : '',
                            'price' => $product->price,
                            'view_count' => $product->view_count,
                            'card_image' => $mainImage && $mainImage->image ? asset('storage/' . $mainImage->image) : asset('images/default_image.webp'),
                            'discount_price' => $product->discount_price,
                            'category_slug' => $categorySlug,
                            'parentSlug' => $parentSlug,
                            'product_types' => $productTypes,
                            'inCart' => $inCart,
                            'inFavorite' => $inFavorite,
                            'inCompare' => $inCompare,
                        ];
                    });
                }

                $slugTranslations = $this->seoPageService->getSlugTranslations($seoPageTranslate->seo_page_id);

                return [
                    'title' => $seoPageTranslate->title,
                    'slug' => $seoPageTranslate->slug,
                    'page_footer' => $seoPageTranslate->page_footer,
                    'meta_title' => $seoPageTranslate->meta_title,
                    'meta_description' => $seoPageTranslate->meta_description,
                    'meta_keywords' => $seoPageTranslate->meta_keywords,
                    'category' => $categoryData,
                    'products' => $products,
                    'slug_translations' => $slugTranslations,
                    'language' => [
                        'code' => $seoPageTranslate->language->lang_code,
                        'name' => $seoPageTranslate->language->name
                    ]
                ];
            });

            if (!$seoPageData) {
                return response()->json([
                    'success' => false,
                    'message' => 'SEO page not found'
                ], 404);
            }

            if (empty($seoPageData['title']) || empty($seoPageData['slug'])) {
                return response()->json([
                    'success' => false,
                    'data' => 'SEO page not found',
                ], 200);
            }

            return response()->json([
                'success' => true,
                'data' => $seoPageData
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching SEO page: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }
}
