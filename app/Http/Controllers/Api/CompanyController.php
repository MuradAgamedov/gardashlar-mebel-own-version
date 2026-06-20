<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Interfaces\Services\Front\CompanyServiceInterface;
use App\Interfaces\Services\Front\ProductServiceInterface;
use App\Http\Resources\Api\CompanyListResource;
use App\Models\Dictionary;
use App\Models\ProductCategory;
use App\Traits\LocaleFromHeader;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class CompanyController extends Controller
{
    use LocaleFromHeader;

    protected CompanyServiceInterface $companyService;
    protected ProductServiceInterface $productService;

    public function __construct(
        CompanyServiceInterface $companyService,
        ProductServiceInterface $productService
    ) {
        $this->companyService = $companyService;
        $this->productService = $productService;
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $languageId = $this->getLanguageIdFromHeader($request);

            $companies = Cache::remember("companies_{$languageId}", 3600, function () use ($languageId) {
                return $this->companyService->getActiveOrderedWithTranslations($languageId);
            });

            return response()->json([
                'success' => true,
                'message' => 'Campaigns retrieved successfully',
                'data' => CompanyListResource::collection($companies),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving campaigns: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function show(Request $request, $slug): JsonResponse
    {
        try {
            $languageId = $this->getLanguageIdFromHeader($request);

            $sort = $request->get('sort', 'view_count');
            $perPage = $request->get('per_page', 12);
            $page = $request->get('page', 1);

            $company = Cache::remember("company_{$slug}_{$languageId}", 3600, function () use ($slug) {
                return $this->companyService->getActiveBySlug($slug);
            });

            if (!$company) {
                return response()->json(['success' => false, 'message' => 'Campaign not found'], 404);
            }

            $translation = $company->translates->firstWhere('language_id', $languageId);

            $slugTranslations = $company->translates->mapWithKeys(fn($t) => [$t->language->lang_code => $t->slug]);

            $sortOptions = [
                ['value' => 'price_asc', 'label' => 'price_asc'],
                ['value' => 'price_desc', 'label' => 'price_desc'],
                ['value' => 'newest', 'label' => 'newest'],
                ['value' => 'oldest', 'label' => 'oldest'],
                ['value' => 'view_count', 'label' => 'view_count'],
            ];

            $sortTranslations = Cache::rememberForever("sort_translations_{$languageId}", function () use ($languageId) {
                $keys = ['price_asc', 'price_desc', 'newest', 'oldest', 'view_count'];
                return array_combine($keys, array_map(fn($k) => Dictionary::getValue($k, $languageId), $keys));
            });

            $productsPaginated = $this->productService->getCompanyProductsPaginated($company->id, $languageId, $sort, $perPage, $page);

            $products = $productsPaginated->map(function ($product) use ($languageId) {
                $tr = $product->translates->first();
                $mainImage = $product->images->where('is_main', true)->first() ?? $product->images->first();

                $categorySlug = $parentSlug = null;
                if ($product->categories && $product->categories->isNotEmpty()) {
                    $category = $product->categories->first();
                    $categorySlug = $category->translates->first()?->slug;
                    if ($category->parent_id) {
                        $parent = ProductCategory::find($category->parent_id);
                        $parentSlug = $parent?->translates()->where('language_id', $languageId)->first()?->slug;
                    }
                }

                $productTypes = $product->types->map(fn($t) => [
                    'id' => $t->id,
                    'title' => $t->translates->first()?->title ?? '',
                ]);

                $user = auth('sanctum')->user();
                return [
                    'id' => $product->id,
                    'title' => $tr?->title ?? '',
                    'slug' => $tr?->slug ?? '',
                    'price' => $product->price,
                    'view_count' => $product->view_count,
                    'card_image' => $mainImage?->image ? asset('storage/' . $mainImage->image) : asset('images/default_image.webp'),
                    'discount_price' => $product->discount_price,
                    'category_slug' => $categorySlug,
                    'parentSlug' => $parentSlug,
                    'product_types' => $productTypes,
                    'inCart' => $user ? \App\Models\Cart::where('user_id', $user->id)->where('product_id', $product->id)->exists() : false,
                    'inFavorite' => $user ? \App\Models\Favorite::where('user_id', $user->id)->where('product_id', $product->id)->exists() : false,
                    'inCompare' => $user ? \App\Models\ProductCompare::where('user_id', $user->id)->where('product_id', $product->id)->exists() : false,
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Campaign retrieved successfully',
                'data' => [
                    'title' => $translation?->title ?? '',
                    'description' => $translation?->description,
                    'inner_image' => $translation?->inner_image ? asset('storage/' . $translation->inner_image) : null,
                    'inner_image_alt_text' => $translation?->inner_image_alt_text,
                    'seo_title' => $translation?->seo_title,
                    'seo_description' => $translation?->seo_description,
                    'seo_keywords' => $translation?->seo_keywords,
                    'products' => $products,
                    'sort_options' => $sortOptions,
                    'sort_translations' => $sortTranslations,
                    'pagination' => [
                        'current_page' => $productsPaginated->currentPage(),
                        'per_page' => $productsPaginated->perPage(),
                        'total' => $productsPaginated->total(),
                        'last_page' => $productsPaginated->lastPage(),
                        'from' => $productsPaginated->firstItem(),
                        'to' => $productsPaginated->lastItem(),
                        'has_more_pages' => $productsPaginated->hasMorePages(),
                    ],
                    'translates' => ['slug' => $slugTranslations],
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving company: ' . $e->getMessage(),
            ], 500);
        }
    }
}
