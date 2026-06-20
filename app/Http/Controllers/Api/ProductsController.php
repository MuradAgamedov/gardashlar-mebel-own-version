<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\ProductCardResource;
use App\Models\Cart;
use App\Models\Favorite;
use App\Models\ProductCategory;
use App\Models\ProductPriceFilter;
use App\Models\ProductColor;
use App\Models\ProductType;
use App\Models\ProductPageCategory;
use App\Models\Product;
use App\Models\ProductCompare;
use App\Models\Dictionary;
use App\Models\Language;
use App\Traits\LocaleFromHeader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;


class ProductsController extends Controller
{
    use LocaleFromHeader;

    /**
     * Custom query parser to handle duplicate keys correctly
     */
    private function getQueryArray(Request $request, string $key): array
    {
        $values = [];

        foreach (explode('&', $request->server('QUERY_STRING', '')) as $param) {
            if ($param === '') continue;

            [$paramKey, $paramValue] = array_pad(explode('=', $param, 2), 2, null);

            $paramKey = urldecode($paramKey);
            $paramValue = urldecode($paramValue ?? '');

            if ($paramKey === $key || $paramKey === $key . '[]') {
                $values[] = $paramValue;
            }
        }

        return array_values(array_filter($values, fn($v) => $v !== ''));
    }

    public function categories(Request $request)
    {
        try {
            $languageId = $this->getLanguageIdFromHeader($request);

            if (!$languageId) {
                return response()->json([
                    'success' => false,
                    'message' => __('messages.invalid_language')
                ], 400);
            }

            $categories = Cache::rememberForever("product_categories_{$languageId}", function () use ($languageId) {
                return ProductCategory::with(['translates' => function($query) use ($languageId) {
                    $query->where('language_id', $languageId);
                }])
                ->active()
                ->ordered()
                ->get();
            });

            // Build hierarchical structure
            $categoriesData = $this->buildHierarchy($categories);

            // Get product price filters
            $priceFilters = Cache::rememberForever("product_price_filters_{$languageId}", function () use ($languageId) {
                return ProductPriceFilter::with(['translates' => function($query) use ($languageId) {
                    $query->where('language_id', $languageId);
                }])
                ->active()
                ->ordered()
                ->get();
            });

            $priceFiltersData = $priceFilters->map(function ($filter) {
                $translation = $filter->translates->first();
                return [
                    'id' => $filter->id,
                    'title' => $translation ? $translation->title : '',
                    'min_price' => $filter->min_price,
                    'max_price' => $filter->max_price
                ];
            });

            // Get product colors
            $productColors = Cache::rememberForever("product_colors", function () {
                return ProductColor::all();
            });

            $productColorsData = $productColors->map(function ($color) {
                return [
                    'id' => $color->id,
                    'color_code' => $color->color_code
                ];
            });

            // Get product types
            $productTypes = Cache::rememberForever("product_types_{$languageId}", function () use ($languageId) {
                return ProductType::with(['translates' => function($query) use ($languageId) {
                    $query->where('language_id', $languageId);
                }])
                ->active()
                ->ordered()
                ->get();
            });

            $productTypesData = $productTypes->map(function ($type) {
                $translation = $type->translates->first();
                return [
                    'id' => $type->id,
                    'title' => $translation ? $translation->title : ''
                ];
            });

            // Get product page categories
            $productPageCategories = Cache::rememberForever("product_page_categories_{$languageId}", function () use ($languageId) {
                return ProductPageCategory::with([
                    'productCategory.translates'        => fn($q) => $q->where('language_id', $languageId),
                    'productCategory.parent.translates' => fn($q) => $q->where('language_id', $languageId),
                ])
                ->active()
                ->ordered()
                ->get();
            });

            $productPageCategoriesData = $productPageCategories->map(function ($pageCategory) {
                $category    = $pageCategory->productCategory;
                $translation = $category ? $category->translates->first() : null;

                $parentSlug = null;
                if ($category && $category->parent_id && $category->relationLoaded('parent') && $category->parent) {
                    $parentSlug = $category->parent->translates->first()?->slug;
                }

                return [
                    'id' => $pageCategory->id,
                    'title' => $translation?->title ?? '',
                    'slug' => $translation ? $translation->slug : '',
                    'parentSlug' => $parentSlug
                ];
            });

            // Sort options
            $sortOptions = [
                ['value' => 'oldest', 'label' => __('messages.oldest')],
                ['value' => 'newest', 'label' => __('messages.newest')],
                ['value' => 'price_asc', 'label' => __('messages.price_asc')],
                ['value' => 'price_desc', 'label' => __('messages.price_desc')],
                ['value' => 'view_count', 'label' => __('messages.view_count')]
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

            // === Yeni: search parametri ===
            $searchQuery = $request->get('search'); // məsələn: ?search=table

            // Pagination və digər filterlər
            $perPage = $request->get('per_page', 12);
            $page = $request->get('page', 1);
            $categorySlug = $request->get('category_slug');
            
            // Fix query parameter parsing for multiple values using custom parser
            $typeIds = $this->getQueryArray($request, 'type_id');
            $filterIds = $this->getQueryArray($request, 'filters');
            $colorIds = $this->getQueryArray($request, 'color');
            
            $sort = $request->get('sort', 'view_count');
            $minPrice = $request->get('min_price');
            $maxPrice = $request->get('max_price');

            // SEO text
            $categorySeoText = null;
            if ($categorySlug) {
                $selectedCategory = ProductCategory::whereHas('translates', function($query) use ($categorySlug, $languageId) {
                    $query->where('slug', $categorySlug)->where('language_id', $languageId);
                })->first();

                if (!$selectedCategory) {
                    return response()->json([
                        'success' => false,
                        'message' => __('messages.category_not_found')
                    ], 404);
                }

                $categoryTranslation = $selectedCategory->translates()->where('language_id', $languageId)->first();
                $categorySeoText = $categoryTranslation ? $categoryTranslation->seo_text : null;
            }

            // Array parameters are now properly handled above with parse_str

            // === Məhsul query ===
            $productsQuery = Product::with([
                'translates'                   => fn($q) => $q->where('language_id', $languageId),
                'images'                       => function($q) use ($colorIds) {
                    $q->where('status', true)->orderBy('order');
                    if (!empty($colorIds) && is_array($colorIds)) {
                        $q->whereIn('product_color_id', $colorIds);
                    }
                },
                'images.translates'            => fn($q) => $q->where('language_id', $languageId),
                'types.translates'             => fn($q) => $q->where('language_id', $languageId),
                'categories.translates'        => fn($q) => $q->where('language_id', $languageId),
                'categories.parent.translates' => fn($q) => $q->where('language_id', $languageId),
            ])
            ->active();

            // === Əgər search varsa, sadə LIKE axtarışı da yoxla ===
            if ($searchQuery) {
                // Debug: Log search query
                \Log::info('Search query: ' . $searchQuery . ' for language: ' . $languageId);
                
                // Sadə LIKE axtarışı - ən azı bir nəticə varsa yoxla
                $simpleSearch = Product::whereHas('translates', function($q) use ($searchQuery, $languageId) {
                    $q->where('language_id', $languageId)
                      ->where('title', 'LIKE', '%' . $searchQuery . '%');
                })
                ->active()
                ->pluck('id');
                
                \Log::info('Simple LIKE search found: ' . $simpleSearch->count() . ' products');
                
                // Əgər sadə axtarış nəticə verirsə, ondan istifadə et
                if ($simpleSearch->isNotEmpty()) {
                    $productsQuery->whereIn('id', $simpleSearch);
                } else {
                    // Əgər sadə axtarış nəticə vermirsə, similar_text ilə yoxla
                    // Bütün aktiv məhsulların tərcümələrini al
                    $allProductTranslates = \App\Models\ProductTranslate::where('language_id', $languageId)
                        ->whereHas('product', function($query) {
                            $query->active();
                        })
                        ->get();

                    // Debug: Log total products found
                    \Log::info('Total products found: ' . $allProductTranslates->count());

                    // Oxşarlıq skorlarını saxlamaq üçün array
                    $similarProducts = [];
                    
                    foreach ($allProductTranslates as $translate) {
                        // Başlıqla oxşarlıq
                        similar_text(strtolower(trim($searchQuery)), strtolower(trim($translate->title)), $titleSimilarity);
                        
                        // Təsvirlə oxşarlıq (varsa)
                        $descriptionSimilarity = 0;
                        if ($translate->description) {
                            similar_text(strtolower(trim($searchQuery)), strtolower(trim($translate->description)), $descriptionSimilarity);
                        }
                        
                        // SEO mətnlərilə oxşarlıq (varsa)
                        $seoDescriptionSimilarity = 0;
                        if ($translate->seo_description) {
                            similar_text(strtolower(trim($searchQuery)), strtolower(trim($translate->seo_description)), $seoDescriptionSimilarity);
                        }
                        
                        // Maksimum oxşarlıq skorunu tap
                        $maxSimilarity = max($titleSimilarity, $descriptionSimilarity, $seoDescriptionSimilarity);
                        
                        // Ən azı 60% oxşarlıq olan məhsulları əlavə et (threshold'u endirdik)
                        if ($maxSimilarity >= 60) {
                            $similarProducts[] = [
                                'product_id' => $translate->product_id,
                                'similarity_score' => $maxSimilarity,
                                'matched_text' => $translate->title,
                                'similarity_type' => $titleSimilarity >= $maxSimilarity ? 'title' : ($descriptionSimilarity >= $maxSimilarity ? 'description' : 'seo_description')
                            ];
                            
                            // Debug: Log matches
                            \Log::info('Match found: ' . $translate->title . ' with similarity: ' . $maxSimilarity . '%');
                        }
                    }
                    
                    // Oxşarlıq skoruna görə sırala
                    usort($similarProducts, function($a, $b) {
                        return $b['similarity_score'] <=> $a['similarity_score'];
                    });
                    
                    // Debug: Log final results
                    \Log::info('Total similar products: ' . count($similarProducts));
                    
                    // Yalnız product_id-ləri götür
                    $similarProductIds = array_column($similarProducts, 'product_id');
                    
                    // Əgər oxşar məhsul varsa, filter tətbiq et
                    if (!empty($similarProductIds)) {
                        $productsQuery->whereIn('id', $similarProductIds)
                            ->orderByRaw('FIELD(id, ' . implode(',', $similarProductIds) . ')');
                    } else {
                        // Əgər heç bir oxşar məhsul tapılmadısa, boş nəticə qaytar
                        $productsQuery->where('id', 0);
                    }
                }
            }

            // === Digər filterlər (category, type, color, price və s.) elə qalır ===
            if ($categorySlug) {
                $category = ProductCategory::whereHas('translates', function($query) use ($categorySlug) {
                    $query->where('slug', $categorySlug);
                })->first();

                if (!$category) {
                    return response()->json([
                        'success' => false,
                        'message' => __('messages.category_not_found')
                    ], 404);
                }

                $children = ProductCategory::where('parent_id', $category->id)->pluck('id');
                $categoryIds = $children->isNotEmpty() ? $children->push($category->id)->toArray() : [$category->id];
                $productsQuery->whereHas('categories', function($query) use ($categoryIds) {
                    $query->whereIn('product_categories.id', $categoryIds);
                });
            }

            if (!empty($typeIds)) {
                $productsQuery->whereHas('types', function($query) use ($typeIds) {
                    $query->whereIn('product_types.id', $typeIds);
                });
            }

            if (!empty($colorIds)) {
                $productsQuery->whereHas('images', function($query) use ($colorIds) {
                    $query->whereIn('product_color_id', $colorIds)->where('status', true);
                });
            }

            // === Qiymət filteri (min/max və ya predefined range-lər) ===
            $appliedMinPrice = null;
            $appliedMaxPrice = null;

            if ($minPrice !== null || $maxPrice !== null) {
                $appliedMinPrice = $minPrice;
                $appliedMaxPrice = $maxPrice;
                if ($appliedMinPrice !== null) $productsQuery->where('price', '>=', $appliedMinPrice);
                if ($appliedMaxPrice !== null) $productsQuery->where('price', '<=', $appliedMaxPrice);
            } elseif (!empty($filterIds)) {
                $priceFilters = ProductPriceFilter::whereIn('id', $filterIds)->get();
                if ($priceFilters->isNotEmpty()) {
                    $appliedMinPrice = $priceFilters->min('min_price');
                    $appliedMaxPrice = $priceFilters->max('max_price');
                    if ($appliedMinPrice !== null) $productsQuery->where('price', '>=', $appliedMinPrice);
                    if ($appliedMaxPrice !== null) $productsQuery->where('price', '<=', $appliedMaxPrice);
                }
            }

            // === Sort əməliyyatları ===
            switch ($sort) {
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
                default:
                    // İlk dəfə çağırıldıqda ən yenilər birinci, ən köhnələr sonda olsun
                    $productsQuery->orderBy('created_at', 'asc');
                    break;
            }



            // === Pagination ===
            $products = $productsQuery->paginate($perPage, ['*'], 'page', $page);

            // Min-max price (bütün məhsullardan)
            $allProducts = Product::active()->get();
            $actualMinPrice = $allProducts->isNotEmpty() ? $allProducts->min('price') : null;
            $actualMaxPrice = $allProducts->isNotEmpty() ? $allProducts->max('price') : null;

            $user        = auth('sanctum')->user();
            $productIds  = $products->pluck('id');
            $cartIds = $favIds = $compareIds = [];
            if ($user && $productIds->isNotEmpty()) {
                $cartIds    = Cart::where('user_id', $user->id)->whereIn('product_id', $productIds)->pluck('product_id')->toArray();
                $favIds     = Favorite::where('user_id', $user->id)->whereIn('product_id', $productIds)->pluck('product_id')->toArray();
                $compareIds = ProductCompare::where('user_id', $user->id)->whereIn('product_id', $productIds)->pluck('product_id')->toArray();
            }
            $request->attributes->set('cartIds', $cartIds);
            $request->attributes->set('favIds', $favIds);
            $request->attributes->set('compareIds', $compareIds);

            $productsData = ProductCardResource::collection($products->getCollection());

            $paginationData = [
                'current_page' => $products->currentPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
                'last_page' => $products->lastPage(),
                'from' => $products->firstItem(),
                'to' => $products->lastItem(),
                'has_more_pages' => $products->hasMorePages()
            ];
    $translatesData = null;

if ($categorySlug) {
    $selectedCategory = ProductCategory::with(['translates', 'parent.translates'])
        ->whereHas('translates', fn($q) => $q->where('slug', $categorySlug))
        ->first();

    if (!$selectedCategory) {
        return response()->json([
            'success' => false,
            'message' => __('messages.category_not_found')
        ], 404);
    }

    $languages = \App\Models\Language::all();

    $slugTranslations       = [];
    $parentSlugTranslations = [];

    foreach ($languages as $lang) {
        $langCode = $lang->lang_code;

        $catTr                      = $selectedCategory->translates->where('language_id', $lang->id)->first();
        $slugTranslations[$langCode] = $catTr?->slug;

        $parentSlug = null;
        if ($selectedCategory->parent_id && $selectedCategory->parent) {
            $parentTr   = $selectedCategory->parent->translates->where('language_id', $lang->id)->first();
            $parentSlug = $parentTr?->slug;
        }
        $parentSlugTranslations[$langCode] = $parentSlug;
    }

    $translatesData = [
        'slug'       => $slugTranslations,
        'parentSlug' => $parentSlugTranslations,
    ];
}

            return response()->json([

                'success' => true,
                'message' => __('messages.product_categories_retrieved_successfully'),
                'data' => [
                    'categories' => $categoriesData,
                    'product_price_filters' => $priceFiltersData,
                    'product_colors' => $productColorsData,
                    'product_types' => $productTypesData,
                    'product_page_categories' => $productPageCategoriesData,
                    'sort_options' => $sortOptions,
                    'sort_translations' => $sortTranslations,
                    'products' => $productsData,
                    'pagination' => $paginationData,
                    'type_ids' => $typeIds,
                    'selected_types' => !empty($typeIds) ? ProductType::with(['translates' => function($query) use ($languageId) {
                        $query->where('language_id', $languageId);
                    }])->whereIn('id', $typeIds)->get()->map(function ($type) {
                        $translation = $type->translates->first();
                        return [
                            'id' => $type->id,
                            'title' => $translation ? $translation->title : ''
                        ];
                    }) : [],
                    'applied_filters' => [
                        'min_price' => $actualMinPrice,
                        'max_price' => $actualMaxPrice
                    ],
                    'seo_text' => $categorySeoText,
                    'translates' => $translatesData
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('messages.error_retrieving_product_categories') . ': ' . $e->getMessage()
            ], 500);
        }
    }


    /**
     * Build hierarchical structure for categories
     */
    private function buildHierarchy($categories)
    {
        $categoryMap = [];
        $rootCategories = [];

        // First pass: create category map
        foreach ($categories as $category) {
            $translation = $category->translates->first();

            // Get parent slug if parent_id exists
            $parentSlug = null;
            if ($category->parent_id) {
                $parentCategory = $categories->where('id', $category->parent_id)->first();
                if ($parentCategory) {
                    $parentTranslation = $parentCategory->translates->first();
                    $parentSlug = $parentTranslation ? $parentTranslation->slug : null;
                }
            }

            $categoryData = [
                'id' => $category->id,
                'title' => $translation ? $translation->title : '',
                'slug' => $translation ? $translation->slug : '',
                'parentSlug' => $parentSlug,
                'children' => []
            ];

            $categoryMap[$category->id] = $categoryData;
        }

        // Second pass: build parent-child relationships
        foreach ($categories as $category) {
            if (!is_null($category->parent_id) && isset($categoryMap[$category->parent_id])) {
                $categoryMap[$category->parent_id]['children'][] = $categoryMap[$category->id];
            }
        }

        // Third pass: collect root categories
        foreach ($categories as $category) {
            if (is_null($category->parent_id)) {
                $rootCategories[] = $categoryMap[$category->id];
            }
        }

        // Sort children by order (using original category order)
        foreach ($categoryMap as $id => $category) {
            usort($categoryMap[$id]['children'], function($a, $b) use ($categories) {
                $catA = $categories->where('id', $a['id'])->first();
                $catB = $categories->where('id', $b['id'])->first();
                return ($catA ? $catA->order : 0) <=> ($catB ? $catB->order : 0);
            });
        }

        // Sort root categories by order (using original category order)
        usort($rootCategories, function($a, $b) use ($categories) {
            $catA = $categories->where('id', $a['id'])->first();
            $catB = $categories->where('id', $b['id'])->first();
            return ($catA ? $catA->order : 0) <=> ($catB ? $catB->order : 0);
        });

        return $rootCategories;
    }


    public function show(Request $request, $slug)
    {
        try {
            $languageId = $this->getLanguageIdFromHeader($request);
            $colorId = $request->get('color_id');
            $categery_slug = $request->get('category_slug');
            if (!$languageId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid language'
                ], 400);
            }

            $productQuery = Product::with([
                'translates'                     => fn($q) => $q->where('language_id', $languageId),
                'types.translates'               => fn($q) => $q->where('language_id', $languageId),
                'tags.translates'                => fn($q) => $q->where('language_id', $languageId),
                'accordions.translates'          => fn($q) => $q->where('language_id', $languageId),
                'details'                        => fn($q) => $q->orderBy('order'),
                'details.translates'             => fn($q) => $q->where('language_id', $languageId),
                'detailValues'                   => fn($q) => $q->orderBy('order'),
                'detailValues.translates'        => fn($q) => $q->where('language_id', $languageId),
                'detailValues.details'           => fn($q) => $q->orderBy('order'),
                'detailValues.details.translates' => fn($q) => $q->where('language_id', $languageId),
                'categories.translates'          => fn($q) => $q->where('language_id', $languageId),
                'categories.parent.translates'   => fn($q) => $q->where('language_id', $languageId),
                'images'                         => fn($q) => $q->where('status', true)->orderBy('order'),
                'images.translates'              => fn($q) => $q->where('language_id', $languageId),
            ])
            ->whereHas('translates', fn($q) => $q->where('slug', $slug));

            // Category slug filtri əlavə et
            if ($categery_slug) {
                $productQuery->whereHas('categories.translates', function($q) use ($categery_slug, $languageId) {
                    $q->where('slug', $categery_slug)
                      ->where('language_id', $languageId);
                });
            }

            $product = $productQuery->first();

            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => __('messages.product_not_found')
                ], 404);
            }

            $translation = $product->translates->first();
            $product->increment('view_count');
            // --- User Data
            $user = auth('sanctum')->user();

            // --- Cart data
            $inCart = [
                'isActive' => false,
                'color_id' => null,
                'count' => null,
            ];
            if ($user) {
                $cartItem = \App\Models\Cart::where('user_id', $user->id)
                    ->where('product_id', $product->id)
                    ->first();
                if ($cartItem) {
                    $inCart = [
                        'isActive' => true,
                        'color_id' => $cartItem->product_color_id ?? null,
                        'count' => $cartItem->count ?? null,
                    ];
                }
            }

            // --- Favorite data
            $inFavorite = [
                'isActive' => false,
                'color_id' => null,
            ];
            if ($user) {
                $favoriteItem = \App\Models\Favorite::where('user_id', $user->id)
                    ->where('product_id', $product->id)
                    ->first();
                if ($favoriteItem) {
                    $inFavorite = [
                        'isActive' => true,
                        'color_id' => $favoriteItem->product_color_id ?? null,
                    ];
                }
            }

            // --- Compare data
            $inCompare = false;
            if ($user) {
                $inCompare = \App\Models\ProductCompare::where('user_id', $user->id)
                    ->where('product_id', $product->id)
                    ->exists();
            }

            // --- Relations
            $productTypes = $product->types->map(fn($t) => [
                'id' => $t->id,
                'title' => optional($t->translates->first())->title ?? '',
            ]);

            $productTags = $product->tags->map(fn($t) => [
                'id' => $t->id,
                'title' => optional($t->translates->first())->title ?? '',
            ]);

            $productAccordions = $product->accordions->map(fn($a) => [
                'id' => $a->id,
                'title' => optional($a->translates->first())->title ?? '',
                'content' => optional($a->translates->first())->description ?? '',
            ]);

            $productDetails = $product->details->map(fn($d) => [
                'id' => $d->id,
                'keyword' => optional($d->translates->first())->keyword ?? '',
                'value' => optional($d->translates->first())->value ?? '',
            ])->filter(fn($d) => $d['keyword'] && $d['value'])->values();

            $productDetailValues = $product->detailValues->map(function ($dv) {
                $translation = $dv->translates->first();
                $details = $dv->details->sortBy('order')->map(fn($d) => [
                    'id'      => $d->id,
                    'keyword' => $d->translates->first()?->keyword ?? '',
                    'value'   => $d->translates->first()?->value ?? '',
                ])->filter(fn($d) => $d['keyword'] && $d['value'])->values();
                return [
                    'id'      => $dv->id,
                    'keyword' => $translation?->keyword ?? '',
                    'details' => $details,
                ];
            });

            // --- Images
            $images = $product->images;
            if ($colorId) {
                $images = $images->where('product_color_id', $colorId);
            }

            $productImages = $images->map(function ($image) use ($languageId) {
                $imgTr = $image->translates->first();
                return [
                    'id' => $image->id,
                    'image_url' => $image->image ? asset('storage/' . $image->image) : null,
                    'is_main' => $image->is_main,
                    'order' => $image->order,
                    'color_id' => $image->product_color_id,
                    'alt_text' => $imgTr ? $imgTr->alt_text : '',
                ];
            });

            // --- Available colors
            $availableColors = $product->images
                ->whereNotNull('product_color_id')
                ->pluck('product_color_id')
                ->unique()
                ->values();

            $availableColorDetails = [];
            if ($availableColors->isNotEmpty()) {
                $availableColorDetails = ProductColor::whereIn('id', $availableColors)
                    ->get()
                    ->map(fn($c) => [
                        'id' => $c->id,
                        'color_code' => $c->color_code,
                    ]);
            }

            // --- Categories
            $productCategories = $product->categories->map(function ($cat) {
                $tr         = $cat->translates->first();
                $parentSlug = null;
                $parentTitle = null;
                if ($cat->parent_id && $cat->relationLoaded('parent') && $cat->parent) {
                    $ptr         = $cat->parent->translates->first();
                    $parentSlug  = $ptr?->slug;
                    $parentTitle = $ptr?->title;
                }
                return [
                    'id'           => $cat->id,
                    'title'        => $tr?->title ?? '',
                    'slug'         => $tr?->slug ?? '',
                    'parent_slug'  => $parentSlug,
                    'parent_title' => $parentTitle,
                ];
            });

            // --- Comments
            $comments = $product->comments()
                ->with('user', 'answers')
                ->where('status', 'approved')
                ->get()
                ->map(fn($c) => [
                    'id' => $c->id,
                    'user_name' => $c->user ? $c->user->name . ' ' . $c->user->surname : 'Unknown',
                    'text' => $c->text,
                    'answers' => $c->answers->pluck('text'),
                ]);


            // --- Product Data
            $productData = [
                'id' => $product->id,
                'title' => $translation ? $translation->title : '',
                'slug' => $translation ? $translation->slug : '',
                'price' => $product->price,
                'discount_price' => $product->discount_price,
                'view_count' => $product->view_count,
                'product_types' => $productTypes,
                'product_tags' => $productTags,
                'product_accordions' => $productAccordions,
                'product_details' => $productDetails,
                'product_detail_values' => $productDetailValues,
                'available_colors' => $availableColorDetails,
                'images' => $productImages,
                'categories' => $productCategories,
                'comments' => $comments,
                'inCart' => $inCart,
                'inFavorite' => $inFavorite,
                'inCompare' => $inCompare,
            ];

            // --- Similar products
            $similarProducts = Product::whereHas('categories', function($q) use ($product) {
                    $q->whereIn('product_categories.id', $product->categories->pluck('id'));
                })
                ->where('id', '!=', $product->id)
                ->active()
                ->with([
                    'translates'                   => fn($q) => $q->where('language_id', $languageId),
                    'images'                       => fn($q) => $q->where('status', true)->orderBy('order'),
                    'images.translates'            => fn($q) => $q->where('language_id', $languageId),
                    'types.translates'             => fn($q) => $q->where('language_id', $languageId),
                    'categories.translates'        => fn($q) => $q->where('language_id', $languageId),
                    'categories.parent.translates' => fn($q) => $q->where('language_id', $languageId),
                ])
                ->take(8)
                ->get();

            // Batch-load cart/fav/compare for similar products
            $simIds        = $similarProducts->pluck('id');
            $simCartMap    = $user && $simIds->isNotEmpty()
                ? Cart::where('user_id', $user->id)->whereIn('product_id', $simIds)->get()->keyBy('product_id')
                : collect();
            $simFavMap     = $user && $simIds->isNotEmpty()
                ? Favorite::where('user_id', $user->id)->whereIn('product_id', $simIds)->get()->keyBy('product_id')
                : collect();
            $simCompareIds = $user && $simIds->isNotEmpty()
                ? ProductCompare::where('user_id', $user->id)->whereIn('product_id', $simIds)->pluck('product_id')->toArray()
                : [];

            $similarData = $similarProducts->map(function ($item) use ($simCartMap, $simFavMap, $simCompareIds) {
                $translation = $item->translates->first();
                $mainImage   = $item->images->where('is_main', true)->first() ?? $item->images->first();
                $mainImageAlt = $mainImage?->translates->first()?->alt_text ?? '';

                $category     = $item->categories->first();
                $categorySlug = $category?->translates->first()?->slug;
                $parentSlug   = ($category?->parent_id && $category->relationLoaded('parent'))
                    ? $category->parent?->translates->first()?->slug
                    : null;

                $cartItem  = $simCartMap->get($item->id);
                $favItem   = $simFavMap->get($item->id);

                return [
                    'id'             => $item->id,
                    'title'          => $translation?->title ?? '',
                    'slug'           => $translation?->slug ?? '',
                    'price'          => $item->price,
                    'discount_price' => $item->discount_price,
                    'card_image'     => $mainImage?->image
                        ? asset('storage/' . $mainImage->image)
                        : asset('images/default_image.webp'),
                    'card_image_alt' => $mainImageAlt,
                    'category_slug'  => $categorySlug,
                    'parentSlug'     => $parentSlug,
                    'inCart'    => ['isActive' => (bool) $cartItem, 'color_id' => $cartItem?->color_id ?? null, 'count' => $cartItem?->count ?? null],
                    'inFavorite' => ['isActive' => (bool) $favItem, 'color_id' => $favItem?->color_id ?? null],
                    'inCompare' => in_array($item->id, $simCompareIds),
                ];
            });
            $languages = \App\Models\Language::all();

            // Reload all translates (without language filter) for slug mapping
            $product->load(['translates', 'categories.translates', 'categories.parent.translates']);

            // === Məhsulun bütün dillərdəki slug-ları ===
            $productSlugTranslations = $languages->mapWithKeys(fn($lang) => [
                $lang->lang_code => $product->translates->where('language_id', $lang->id)->first()?->slug,
            ])->toArray();

            // === Məhsulun əsas kateqoriyası və onun parent slug-ları ===
            $categorySlugTranslations       = [];
            $parentCategorySlugTranslations = [];

            if ($product->categories->isNotEmpty()) {
                $mainCategory = $product->categories->first();

                foreach ($languages as $lang) {
                    $catTr                                    = $mainCategory->translates->where('language_id', $lang->id)->first();
                    $categorySlugTranslations[$lang->lang_code] = $catTr?->slug;

                    $parentSlugForLang                                      = null;
                    if ($mainCategory->parent_id && $mainCategory->relationLoaded('parent') && $mainCategory->parent) {
                        $parentTr          = $mainCategory->parent->translates->where('language_id', $lang->id)->first();
                        $parentSlugForLang = $parentTr?->slug;
                    }
                    $parentCategorySlugTranslations[$lang->lang_code] = $parentSlugForLang;
                }
            }

                // 🔹 Hamısını bir yerdə cəmlə
                $translatesData = [
                    'productSlug' => $productSlugTranslations,
                    'categorySlug' => $categorySlugTranslations,
                    'parentCategorySlug' => $parentCategorySlugTranslations
                ];

            return response()->json([
                'success' => true,
                'message' => 'Product retrieved successfully',
                'data' => [
                    'product' => $productData,
                    'similar_products' => $similarData,
                    'translates' => $translatesData

                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving product: ' . $e->getMessage()
            ], 500);
        }
    }



    public function details(Request $request, $id)
    {
        try {
            $languageId = $this->getLanguageIdFromHeader($request);

            if (!$languageId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid language'
                ], 400);
            }

            // Product with translations and relations
            $product = Product::with([
                'translates' => function($q) use ($languageId) {
                    $q->where('language_id', $languageId);
                },
                'images' => function($q) {
                    $q->where('status', true)->orderBy('order');
                },
                'images.translates' => function($q) use ($languageId) {
                    $q->where('language_id', $languageId);
                },
                'categories.translates' => function($q) use ($languageId) {
                    $q->where('language_id', $languageId);
                },
                'types.translates' => function($q) use ($languageId) {
                    $q->where('language_id', $languageId);
                },
                'details.translates' => function($q) use ($languageId) {
                    $q->where('language_id', $languageId);
                }
            ])->findOrFail($id);

            $translation = $product->translates->first();

            // ✳️ Product details
            $detailsData = $product->details->map(function ($detail) {
                $translation = $detail->translates->first();
                return [
                    'id' => $detail->id,
                    'keyword' => $translation ? $translation->keyword : '',
                    'value' => $translation ? $translation->value : ''
                ];
            });

            // ✳️ User
            $user = auth('sanctum')->user();

            // ✳️ inCart
            $inCart = [
                'isActive' => false,
                'color_id' => null,
                'count' => null,
            ];

            if ($user) {
                $cartItem = \App\Models\Cart::where('user_id', $user->id)
                    ->where('product_id', $product->id)
                    ->first();

                if ($cartItem) {
                    $inCart['isActive'] = true;
                    $inCart['color_id'] = $cartItem->product_color_id ?? null;
                    $inCart['count'] = $cartItem->count ?? null;
                }
            }

            // ✳️ inFavorite
            $inFavorite = [
                'isActive' => false,
                'color_id' => null,
            ];

            if ($user) {
                $favoriteItem = \App\Models\Favorite::where('user_id', $user->id)
                    ->where('product_id', $product->id)
                    ->first();

                if ($favoriteItem) {
                    $inFavorite['isActive'] = true;
                    $inFavorite['color_id'] = $favoriteItem->product_color_id ?? null;
                }
            }

            // ✳️ inCompare
            $inCompare = false;
            if ($user) {
                $inCompare = \App\Models\ProductCompare::where('user_id', $user->id)
                    ->where('product_id', $product->id)
                    ->exists();
            }

            // ✳️ Similar products — only id/title/card_image needed
            $similarProductsQuery = Product::with([
                'translates' => fn($q) => $q->where('language_id', $languageId),
                'images'     => fn($q) => $q->where('status', true)->orderBy('order'),
            ])
            ->active()
            ->whereHas('categories', function($q) use ($product) {
                $q->whereIn('product_categories.id', $product->categories->pluck('id'));
            })
            ->where('id', '!=', $product->id)
            ->limit(8);

            $similarProducts = $similarProductsQuery->get();

            $similarProductsData = $similarProducts->map(function ($product) {
                $translation = $product->translates->first();
                $mainImage = $product->images->where('is_main', true)->first() ?? $product->images->first();

                return [
                    'id'         => $product->id,
                    'title'      => $translation?->title ?? '',
                    'card_image' => $mainImage?->image
                        ? asset('storage/' . $mainImage->image)
                        : asset('images/default_image.webp'),
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Product details retrieved successfully',
                'data' => [
                    'product' => [
                        'id' => $product->id,
                        'title' => $translation ? $translation->title : '',
                        'slug' => $translation ? $translation->slug : '',
                        'price' => $product->price,
                        'view_count' => $product->view_count,
                        'discount_price' => $product->discount_price,
                        'images' => $product->images->map(fn($img) => asset('storage/' . $img->image)),
                        'images_detailed' => $product->images->map(function ($img) use ($languageId) {
                            $tr = $img->translates->first();
                            return [
                                'image_url' => $img->image ? asset('storage/' . $img->image) : null,
                                'alt_text' => $tr ? $tr->alt_text : '',
                                'is_main' => $img->is_main,
                                'order' => $img->order,
                                'color_id' => $img->product_color_id,
                            ];
                        }),
                        'details' => $detailsData,
                        'inCart' => $inCart,
                        'inFavorite' => $inFavorite,
                        'inCompare' => $inCompare
                    ],
                    'similar_products' => $similarProductsData
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving product details: ' . $e->getMessage()
            ], 500);
        }
    }




    public function detailValues(Request $request, $id)
    {
        try {
            $languageId = $this->getLanguageIdFromHeader($request);

            if (!$languageId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid language'
                ], 400);
            }

            $product = Product::findOrFail($id);
            $detailValues = $product->detailValues()->with(['translates' => function($query) use ($languageId) {
                $query->where('language_id', $languageId);
            }])->get();

            $detailValuesData = $detailValues->map(function ($detailValue) {
                $translation = $detailValue->translates->first();
                return [
                    'id' => $detailValue->id,
                    'keyword' => $translation ? $translation->keyword : ''
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Product detail values retrieved successfully',
                'data' => $detailValuesData
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving product detail values: ' . $e->getMessage()
            ], 500);
        }
    }


    public function detailValueDetails(Request $request, $id, $detailValueId)
    {
        try {
            $languageId = $this->getLanguageIdFromHeader($request);

            if (!$languageId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid language'
                ], 400);
            }

            $product = Product::findOrFail($id);
            $detailValue = $product->detailValues()->findOrFail($detailValueId);

            $details = $detailValue->details()->with(['translates' => function($query) use ($languageId) {
                $query->where('language_id', $languageId);
            }])->get();

            $detailsData = $details->map(function ($detail) {
                $translation = $detail->translates->first();
                return [
                    'id' => $detail->id,
                    'keyword' => $translation ? $translation->keyword : '',
                    'value' => $translation ? $translation->value : ''
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Product detail value details retrieved successfully',
                'data' => $detailsData
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving product detail value details: ' . $e->getMessage()
            ], 500);
        }
    }








    public function categoriesOnly(Request $request)
    {
        try {
            $languageId = $this->getLanguageIdFromHeader($request);

            // Get categories with translations
            $categories = Cache::rememberForever("product_categories_{$languageId}", function () use ($languageId) {
                return ProductCategory::with(['translates' => function($query) use ($languageId) {
                    $query->where('language_id', $languageId);
                }])
                ->active()
                ->ordered()
                ->get();
            });

            // Build hierarchical structure
            $categoriesData = $this->buildHierarchy($categories);

            return response()->json([
                'success' => true,
                'message' => 'Categories retrieved successfully',
                'data' => [
                    'categories' => $categoriesData
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving categories: ' . $e->getMessage()
            ], 500);
        }
    }


    /**
 * Get category details by slug
 */
public function categoryBySlug(Request $request, $slug)
{
    try {
        $languageId = $this->getLanguageIdFromHeader($request);

        if (!$languageId) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid language'
            ], 400);
        }

        $category = ProductCategory::with([
            'translates',
            'parent.translates',
        ])
        ->whereHas('translates', function($query) use ($languageId, $slug) {
            $query->where('language_id', $languageId)
                  ->where('slug', $slug);
        })
        ->firstOrFail();

        $translation = $category->translates->where('language_id', $languageId)->first();

        // Get all languages for translates structure
        $languages = \App\Models\Language::all();
        
        // Build translates structure
        $translatesData = [
            'slug' => [],
            'parent_slug' => []
        ];

        foreach ($languages as $lang) {
            $langCode = $lang->lang_code;
            
            // Category slug
            $categoryTr = $category->translates->where('language_id', $lang->id)->first();
            $translatesData['slug'][$langCode] = $categoryTr ? $categoryTr->slug : null;
            
            // Parent slug (if category has parent)
            $parentSlug = null;
            if ($category->parent_id && $category->parent) {
                $parentTr   = $category->parent->translates->where('language_id', $lang->id)->first();
                $parentSlug = $parentTr?->slug;
            }
            $translatesData['parent_slug'][$langCode] = $parentSlug;
        }

        $data = [
            'id' => $category->id,
            'title' => $translation->title ?? '',
            'slug' => $translation->slug ?? '',
            'seo_title' => $translation->seo_title ?? '',
            'seo_description' => $translation->seo_description ?? '',
            'seo_keywords' => $translation->seo_keywords ?? '',
            'image' => asset('storage/' . $category->image),
            'image_alt_text' => $translation->image_alt_text ?? '',
            'translates' => $translatesData
        ];

        return response()->json([
            'success' => true,
            'data' => $data
        ]);

    } catch (\Exception $e) {
        return response([
            'success' => false,
            'message' => 'Error retrieving category: ' . $e->getMessage()
        ], 500);
    }
}

    /**
     * Get product details by slug with minimal required fields
     */
    public function productBySlug(Request $request, $slug)
    {
        try {
            $languageId = $this->getLanguageIdFromHeader($request);

            if (!$languageId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid language'
                ], 400);
            }

            // Get product with all translations and relationships
            $product = Product::with([
                'translates',
                'categories.translates',
                'categories.parent.translates',
                'images' => function($query) {
                    $query->where('is_main', true)
                          ->where('status', true)
                          ->with(['translates']);
                }
            ])
            ->whereHas('translates', function($query) use ($languageId, $slug) {
                $query->where('language_id', $languageId)
                      ->where('slug', $slug);
            })
            ->firstOrFail();

            $translation = $product->translates->where('language_id', $languageId)->first();
            $mainImage = $product->images->first();

            // Get category title
            $categoryTitle = '';
            if ($product->categories->isNotEmpty()) {
                $category = $product->categories->first();
                $categoryTranslation = $category->translates->where('language_id', $languageId)->first();
                $categoryTitle = $categoryTranslation ? $categoryTranslation->title : '';
            }

            // Get image alt text from translation
            $imageAltText = '';
            if ($mainImage && $mainImage->translates->isNotEmpty()) {
                $imageAltText = $mainImage->translates->where('language_id', $languageId)->first()->alt_text ?? '';
            }

            // Get all languages for translates structure
            $languages = \App\Models\Language::all();
            
            // Build translates structure
            $translatesData = [
                'slug' => [],
                'category_slug' => [],
                'parent_slug' => []
            ];

            foreach ($languages as $lang) {
                $langCode = $lang->lang_code;
                
                // Product slug
                $productTr = $product->translates->where('language_id', $lang->id)->first();
                $translatesData['slug'][$langCode] = $productTr ? $productTr->slug : null;
                
                // Category slug
                $categorySlug = null;
                if ($product->categories->isNotEmpty()) {
                    $category = $product->categories->first();
                    $categoryTr = $category->translates->where('language_id', $lang->id)->first();
                    $categorySlug = $categoryTr ? $categoryTr->slug : null;
                }
                $translatesData['category_slug'][$langCode] = $categorySlug;
                
                // Parent slug (if category has parent)
                $parentSlug = null;
                if ($product->categories->isNotEmpty()) {
                    $category = $product->categories->first();
                    if ($category->parent_id && $category->parent) {
                        $parentTr   = $category->parent->translates->where('language_id', $lang->id)->first();
                        $parentSlug = $parentTr?->slug;
                    }
                }
                $translatesData['parent_slug'][$langCode] = $parentSlug;
            }

            // Prepare response data with only required fields
            $data = [
                'title' => $translation->title ?? '',
                'seo_title' => $translation->seo_title ?? '',
                'seo_description' => $translation->seo_description ?? '',
                'seo_keywords' => $translation->seo_keywords ?? '',
                'image' => $mainImage ? asset('storage/' . $mainImage->image) : null,
                'image_alt_text' => $imageAltText,
                'category_title' => $categoryTitle,
                'price' => $product->price,
                'discount_price' => $product->discount_price,
                'translates' => $translatesData
            ];

            return response()->json([
                'success' => true,
                'message' => 'Product details retrieved successfully',
                'data' => $data
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving product: ' . $e->getMessage()
            ], 500);
        }
    }
}
