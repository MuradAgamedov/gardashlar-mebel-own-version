<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Interfaces\Services\Front\CompanyServiceInterface;
use App\Interfaces\Services\Front\ProductServiceInterface;
use App\Http\Resources\Api\CompanyListResource;
use App\Http\Resources\Api\ProductCardResource;
use App\Models\Cart;
use App\Models\Favorite;
use App\Models\Dictionary;
use App\Models\ProductCompare;
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

            return $this->success(CompanyListResource::collection($companies), 'Campaigns retrieved successfully');

        } catch (\Exception $e) {
            return $this->serverError('Error retrieving campaigns: ' . $e->getMessage());
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
                return $this->notFound('Campaign not found');
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

            $user = auth('sanctum')->user();
            $productIds = $productsPaginated->pluck('id');
            $cartIds = $favIds = $compareIds = [];
            if ($user && $productIds->isNotEmpty()) {
                $cartIds    = Cart::where('user_id', $user->id)->whereIn('product_id', $productIds)->pluck('product_id')->toArray();
                $favIds     = Favorite::where('user_id', $user->id)->whereIn('product_id', $productIds)->pluck('product_id')->toArray();
                $compareIds = ProductCompare::where('user_id', $user->id)->whereIn('product_id', $productIds)->pluck('product_id')->toArray();
            }
            $request->attributes->set('cartIds', $cartIds);
            $request->attributes->set('favIds', $favIds);
            $request->attributes->set('compareIds', $compareIds);

            $products = ProductCardResource::collection($productsPaginated->getCollection());

            return $this->success([
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
            ], 'Campaign retrieved successfully');

        } catch (\Exception $e) {
            return $this->serverError('Error retrieving company: ' . $e->getMessage());
        }
    }
}
