<?php

namespace App\Http\Controllers\Api;
use App\Models\Dictionary;
use App\Models\Product;
use App\Models\ProductCategory;

use App\Http\Controllers\Controller;
use App\Traits\LocaleFromHeader;
use App\Models\Company;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

/**
 * @OA\Tag(name="Companies", description="API Endpoints for Companies")
 */
class CompanyController extends Controller
{
    use LocaleFromHeader;

  
    public function index(Request $request)
    {
        try {
            // Get language ID from header
            $languageId = $this->getLanguageIdFromHeader($request);
            
            // Validate language ID
            $language = Language::find($languageId);
            if (!$language) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid language ID'
                ], 400);
            }

            // Get companies with cache
            $companies = Cache::remember("companies_{$languageId}", 3600, function () use ($languageId) {
                return Company::with(['translates' => function($query) use ($languageId) {
                    $query->where('language_id', $languageId);
                }])
                ->active()
                ->ordered()
                ->get();
            });

            $companiesData = $companies->map(function ($company) {
                $translation = $company->translates->first();
                
                return [
                    'id' => $company->id,
                    'slug' => $translation ? $translation->slug : null,
                    'title' => $translation ? $translation->title : null,
                    'card_image_url' => $translation && $translation->card_image ? asset('storage/' . $translation->card_image) : null,
                    'card_image_alt_text' => $translation ? $translation->card_image_alt_text : null,
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Companies retrieved successfully',
                'data' => $companiesData
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving companies: ' . $e->getMessage()
            ], 500);
        }
    }

 public function show(Request $request, $slug)
{
    try {
        $languageId = $this->getLanguageIdFromHeader($request);

        // Validate language ID
        $language = Language::find($languageId);
        if (!$language) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid language ID'
            ], 400);
        }

        $sort = $request->get('sort', 'view_count');
        $perPage = $request->get('per_page', 12);
        $page = $request->get('page', 1);

        // 🔹 Şirkəti tapırıq (bütün dillərlə birlikdə)
        $company = Cache::remember("company_{$slug}_{$languageId}", 3600, function () use ($slug, $languageId) {
            return Company::with([
                    'translates.language'
                ])
                ->active()
                ->whereHas('translates', function($q) use ($slug) {
                    $q->where('slug', $slug);
                })
                ->first();
        });

        if (!$company) {
            return response()->json([
                'success' => false,
                'message' => 'Company not found'
            ], 404);
        }

        $translation = $company->translates->firstWhere('language_id', $languageId);

        // 🔹 Şirkətin bütün dillərdəki slug-ları
        $slugTranslations = $company->translates->mapWithKeys(function ($t) {
            return [$t->language->lang_code => $t->slug];
        });

        // Sort options & translations
        $sortOptions = [
            ['value' => 'price_asc', 'label' => 'price_asc'],
            ['value' => 'price_desc', 'label' => 'price_desc'],
            ['value' => 'newest', 'label' => 'newest'],
            ['value' => 'oldest', 'label' => 'oldest'],
            ['value' => 'view_count', 'label' => 'view_count']
        ];

        $sortTranslations = Cache::rememberForever("sort_translations_{$languageId}", function () use ($languageId) {
            $sortKeywords = [
                'price_asc' => 'price_asc',
                'price_desc' => 'price_desc',
                'newest' => 'newest',
                'oldest' => 'oldest',
                'view_count' => 'view_count'
            ];

            $sortData = [];
            foreach ($sortKeywords as $key => $keyword) {
                $sortData[$key] = Dictionary::getValue($keyword, $languageId);
            }

            return $sortData;
        });

        // Products query
        $productsQuery = Product::query()
            ->whereHas('companyProducts', function($q) use ($company) {
                $q->where('company_id', $company->id);
            })
            ->with([
                'translates' => function($q) use ($languageId) {
                    $q->where('language_id', $languageId);
                },
                'images' => function($q) {
                    $q->where('status', true)->orderBy('order');
                },
                'types.translates' => function($q) use ($languageId) {
                    $q->where('language_id', $languageId);
                },
                'categories.translates' => function($q) use ($languageId) {
                    $q->where('language_id', $languageId);
                }
            ])
            ->active();

        switch($sort) {
            case 'price_asc':
                $productsQuery->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $productsQuery->orderBy('price', 'desc');
                break;
            case 'newest':
                $productsQuery->orderBy('created_at', 'desc');
                break;
            case 'oldest':
                $productsQuery->orderBy('created_at', 'asc');
                break;
            case 'view_count':
            default:
                $productsQuery->orderBy('view_count', 'desc');
                break;
        }

        $productsPaginated = $productsQuery->paginate($perPage, ['*'], 'page', $page);

        // Map products
        $products = $productsPaginated->map(function($product) use ($languageId) {
            $translation = $product->translates->first();
            $mainImage = $product->images->where('is_main', true)->first() ?? $product->images->first();

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

            $productTypes = $product->types->map(function ($type) {
                $translation = $type->translates->first();
                return [
                    'id' => $type->id,
                    'title' => $translation ? $translation->title : ''
                ];
            });

            $user = auth('sanctum')->user();
            $inCart = false;
            $inFavorite = false;
            $inCompare = false;

            if ($user) {
                $inCart = \App\Models\Cart::where('user_id', $user->id)
                    ->where('product_id', $product->id)
                    ->exists();

                $inFavorite = \App\Models\Favorite::where('user_id', $user->id)
                    ->where('product_id', $product->id)
                    ->exists();

                $inCompare = \App\Models\ProductCompare::where('user_id', $user->id)
                    ->where('product_id', $product->id)
                    ->exists();
            }

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

        $paginationData = [
            'current_page' => $productsPaginated->currentPage(),
            'per_page' => $productsPaginated->perPage(),
            'total' => $productsPaginated->total(),
            'last_page' => $productsPaginated->lastPage(),
            'from' => $productsPaginated->firstItem(),
            'to' => $productsPaginated->lastItem(),
            'has_more_pages' => $productsPaginated->hasMorePages()
        ];

        $companyData = [
            'title' => $translation ? $translation->title : '',
            'description' => $translation ? $translation->description : null,
            'inner_image' => $translation && $translation->inner_image ? asset('storage/' . $translation->inner_image) : null,
            'inner_image_alt_text' => $translation ? $translation->inner_image_alt_text : null,
            'seo_title' => $translation ? $translation->seo_title : null,
            'seo_description' => $translation ? $translation->seo_description : null,
            'seo_keywords' => $translation ? $translation->seo_keywords : null,
            'products' => $products,
            'sort_options' => $sortOptions,
            'sort_translations' => $sortTranslations,
            'pagination' => $paginationData,
            'translates' => [
                'slug' => $slugTranslations
            ]
        ];

        return response()->json([
            'success' => true,
            'message' => 'Company retrieved successfully',
            'data' => $companyData
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error retrieving company: ' . $e->getMessage()
        ], 500);
    }
}

    
    

}
