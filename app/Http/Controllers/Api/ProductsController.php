<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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

    public function categories(Request $request)
    {
        try {
            $languageId = $this->getLanguageIdFromHeader($request);
            
            if (!$languageId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid language'
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
                return ProductPageCategory::with(['productCategory.translates' => function($query) use ($languageId) {
                    $query->where('language_id', $languageId);
                }])
                ->active()
                ->ordered()
                ->get();
            });
    
            $productPageCategoriesData = $productPageCategories->map(function ($pageCategory) use ($languageId) {
                $category = $pageCategory->productCategory;
                $translation = $category ? $category->translates->first() : null;
                
                $parentSlug = null;
                if ($category && $category->parent_id) {
                    $parentCategory = ProductCategory::find($category->parent_id);
                    if ($parentCategory) {
                        $parentTranslation = $parentCategory->translates()->where('language_id', $languageId)->first();
                        $parentSlug = $parentTranslation ? $parentTranslation->slug : null;
                    }
                }
                
                return [
                    'id' => $pageCategory->id,
                    'title' => $translation ? $translation->title : '',
                    'slug' => $translation ? $translation->slug : '',
                    'parentSlug' => $parentSlug
                ];
            });
    
            // Sort options
            $sortOptions = [
                ['value' => 'oldest', 'label' => 'oldest'],
                ['value' => 'newest', 'label' => 'newest'],
                ['value' => 'price_asc', 'label' => 'price_asc'],
                ['value' => 'price_desc', 'label' => 'price_desc'],
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
    
            // === Yeni: search parametri ===
            $searchQuery = $request->get('search'); // məsələn: ?search=table
    
            // Pagination və digər filterlər
            $perPage = $request->get('per_page', 12);
            $page = $request->get('page', 1);
            $categorySlug = $request->get('category_slug');
            $typeIds = $request->get('type_id', []);
            $filterIds = $request->get('filters', []);
            $colorIds = $request->get('color', []);
            $sort = $request->get('sort', 'view_count');
            $minPrice = $request->get('min_price');
            $maxPrice = $request->get('max_price');
    
            // SEO text
            $categorySeoText = null;
            if ($categorySlug) {
                $selectedCategory = ProductCategory::whereHas('translates', function($query) use ($categorySlug, $languageId) {
                    $query->where('slug', $categorySlug)->where('language_id', $languageId);
                })->first();
                
                if ($selectedCategory) {
                    $categoryTranslation = $selectedCategory->translates()->where('language_id', $languageId)->first();
                    $categorySeoText = $categoryTranslation ? $categoryTranslation->seo_text : null;
                }
            }
    
            // Convert filters to arrays
            if (!is_array($filterIds)) $filterIds = $filterIds ? [$filterIds] : [];
            if (!is_array($colorIds)) $colorIds = $colorIds ? [$colorIds] : [];
            if (!is_array($typeIds)) $typeIds = $typeIds ? [$typeIds] : [];
    
            // === Məhsul query ===
            $productsQuery = Product::with([
                'translates' => function($query) use ($languageId) {
                    $query->where('language_id', $languageId);
                },
                'images' => function($query) use ($colorIds) {
                    $query->where('status', true)->orderBy('order');
                    if (!empty($colorIds) && is_array($colorIds)) {
                        $query->whereIn('product_color_id', $colorIds);
                    }
                },
                'images.translates' => function($query) use ($languageId) {
                    $query->where('language_id', $languageId);
                },
                'types.translates' => function($query) use ($languageId) {
                    $query->where('language_id', $languageId);
                },
                'categories.translates' => function($query) use ($languageId) {
                    $query->where('language_id', $languageId);
                }
            ])
            ->active();
    
            // === Əgər search varsa, title-larda axtar ===
            if ($searchQuery) {
                $productsQuery->whereHas('translates', function($query) use ($languageId, $searchQuery) {
                    $query->where('language_id', $languageId)
                          ->where('title', 'like', '%' . $searchQuery . '%');
                });
            }
    
            // === Digər filterlər (category, type, color, price və s.) elə qalır ===
            if ($categorySlug) {
                $category = ProductCategory::whereHas('translates', function($query) use ($categorySlug) {
                    $query->where('slug', $categorySlug);
                })->first();
                
                if ($category) {
                    $children = ProductCategory::where('parent_id', $category->id)->pluck('id');
                    $categoryIds = $children->isNotEmpty() ? $children->push($category->id)->toArray() : [$category->id];
                    $productsQuery->whereHas('categories', function($query) use ($categoryIds) {
                        $query->whereIn('product_categories.id', $categoryIds);
                    });
                }
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
    
            $productsData = $products->map(function ($product) use ($languageId) {
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
                    'card_image_alt' => $mainImageAlt,
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
    $selectedCategory = ProductCategory::whereHas('translates', function($query) use ($categorySlug) {
        $query->where('slug', $categorySlug);
    })->first();

    if ($selectedCategory) {
        // bütün dilləri çək
        $languages = \App\Models\Language::all();

        $slugTranslations = [];
        $parentSlugTranslations = [];

        foreach ($languages as $lang) {
            $langCode = $lang->lang_code; // YALNIZ BİR PROPERTY istifadə et

            $catTr = $selectedCategory->translates()->where('language_id', $lang->id)->first();
            $slugTranslations[$langCode] = $catTr ? $catTr->slug : null;

            $parentSlugTranslations[$langCode] = null;
            if ($selectedCategory->parent_id) {
                $parent = ProductCategory::find($selectedCategory->parent_id);
                if ($parent) {
                    $parentTr = $parent->translates()->where('language_id', $lang->id)->first();
                    $parentSlugTranslations[$langCode] = $parentTr ? $parentTr->slug : null;
                }
            }
        }


        $translatesData = [
            'slug' => $slugTranslations,
            'parentSlug' => $parentSlugTranslations
        ];
    }
}

            return response()->json([
                'success' => true,
                'message' => 'Product categories retrieved successfully',
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
                'message' => 'Error retrieving product categories: ' . $e->getMessage()
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
                'translates' => fn($q) => $q->where('language_id', $languageId),
                'types.translates' => fn($q) => $q->where('language_id', $languageId),
                'tags.translates' => fn($q) => $q->where('language_id', $languageId),
                'accordions.translates' => fn($q) => $q->where('language_id', $languageId),
                'details' => fn($q) => $q->orderBy('order'),
                'details.translates' => fn($q) => $q->where('language_id', $languageId),
                'detailValues' => fn($q) => $q->orderBy('order'),
                'detailValues.translates' => fn($q) => $q->where('language_id', $languageId),
                'detailValues.details' => fn($q) => $q->orderBy('order'),
                'detailValues.details.translates' => fn($q) => $q->where('language_id', $languageId),
                'categories.translates' => fn($q) => $q->where('language_id', $languageId),
                'images' => fn($q) => $q->where('status', true)->orderBy('order'),
                'images.translates' => fn($q) => $q->where('language_id', $languageId),
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
                    'message' => 'Product not found'
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
    
            $productDetailValues = $product->detailValues->map(function ($dv) use ($languageId) {
                $translation = $dv->translates->first();
                $details = $dv->details()
                    ->with(['translates' => fn($q) => $q->where('language_id', $languageId)])
                    ->orderBy('order')
                    ->get()
                    ->map(fn($d) => [
                        'id' => $d->id,
                        'keyword' => optional($d->translates->first())->keyword ?? '',
                        'value' => optional($d->translates->first())->value ?? '',
                    ])->filter(fn($d) => $d['keyword'] && $d['value'])->values();
                return [
                    'id' => $dv->id,
                    'keyword' => $translation ? $translation->keyword : '',
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
            $productCategories = $product->categories->map(function ($cat) use ($languageId) {
                $tr = $cat->translates->first();
                $parentSlug = null;
                $parentTitle = null;
                if ($cat->parent_id) {
                    $parent = ProductCategory::find($cat->parent_id);
                    if ($parent) {
                        $ptr = $parent->translates()->where('language_id', $languageId)->first();
                        $parentSlug = $ptr ? $ptr->slug : null;
                        $parentTitle = $ptr ? $ptr->title : null;
                    }
                }
                return [
                    'id' => $cat->id,
                    'title' => $tr ? $tr->title : '',
                    'slug' => $tr ? $tr->slug : '',
                    'parent_slug' => $parentSlug,
                    'parent_title' => $parentTitle,
                ];
            });
    
            // --- Comments
            $comments = $product->comments()
                ->with('user', 'answers')
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
                ->take(8)
                ->get();
    
            $similarData = $similarProducts->map(function ($item) use ($languageId, $user) {
                $translation = $item->translates()->where('language_id', $languageId)->first();
                $mainImage = $item->images()->where('status', true)->orderBy('order')->first();
                $mainImageAlt = '';
                if ($mainImage) {
                    $tr = $mainImage->translates()->where('language_id', $languageId)->first();
                    $mainImageAlt = $tr ? $tr->alt_text : '';
                }
    
                // Category slug və parent slug
                $categorySlug = null;
                $parentSlug = null;
                if ($item->categories->isNotEmpty()) {
                    $category = $item->categories->first();
                    $categoryTranslation = $category->translates()->where('language_id', $languageId)->first();
                    $categorySlug = $categoryTranslation ? $categoryTranslation->slug : null;
                    
                    if ($category->parent_id) {
                        $parentCategory = ProductCategory::find($category->parent_id);
                        if ($parentCategory) {
                            $parentTranslation = $parentCategory->translates()->where('language_id', $languageId)->first();
                            $parentSlug = $parentTranslation ? $parentTranslation->slug : null;
                        }
                    }
                }
    
                // same structure
                $inCart = [
                    'isActive' => false,
                    'color_id' => null,
                    'count' => null,
                ];
                if ($user) {
                    $cartItem = \App\Models\Cart::where('user_id', $user->id)->where('product_id', $item->id)->first();
                    if ($cartItem) {
                        $inCart['isActive'] = true;
                        $inCart['color_id'] = $cartItem->product_color_id ?? null;
                        $inCart['count'] = $cartItem->count ?? null;
                    }
                }
    
                $inFavorite = [
                    'isActive' => false,
                    'color_id' => null,
                ];
                if ($user) {
                    $favItem = \App\Models\Favorite::where('user_id', $user->id)->where('product_id', $item->id)->first();
                    if ($favItem) {
                        $inFavorite['isActive'] = true;
                        $inFavorite['color_id'] = $favItem->product_color_id ?? null;
                    }
                }
    
                $inCompare = false;
                if ($user) {
                    $inCompare = \App\Models\ProductCompare::where('user_id', $user->id)
                        ->where('product_id', $item->id)
                        ->exists();
                }
    
                return [
                    'id' => $item->id,
                    'title' => $translation ? $translation->title : '',
                    'slug' => $translation ? $translation->slug : '',
                    'price' => $item->price,
                    'discount_price' => $item->discount_price,
                    'card_image' => $mainImage && $mainImage->image
                        ? asset('storage/' . $mainImage->image)
                        : asset('images/default_image.webp'),
                    'card_image_alt' => $mainImageAlt,
                    'category_slug' => $categorySlug,
                    'parentSlug' => $parentSlug,
                    'inCart' => $inCart,
                    'inFavorite' => $inFavorite,
                    'inCompare' => $inCompare,
                ];
            });
            $slugTranslations = [];
            $languages = \App\Models\Language::all();

            foreach ($languages as $lang) {
                $translationForLang = $product->translates()
                    ->where('language_id', $lang->id)
                    ->first();

                $slugTranslations[$lang->lang_code] = $translationForLang ? $translationForLang->slug : null;
            }
                $translatesData = [
                    'slug' => $slugTranslations
                ];
                            return response()->json([
                'success' => true,
                'message' => 'Product retrieved successfully',
                'data' => [
                    'product' => $productData,
                    'similar_products' => $similarData,
                    'translates' => $translatesData,

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
    
            // ✳️ Similar products
            $similarProductsQuery = Product::with([
                'translates' => function($query) use ($languageId) {
                    $query->where('language_id', $languageId);
                },
                'images' => function($query) {
                    $query->where('status', true)->orderBy('order');
                },
                'images.translates' => function($query) use ($languageId) {
                    $query->where('language_id', $languageId);
                },
                'types.translates' => function($query) use ($languageId) {
                    $query->where('language_id', $languageId);
                },
                'categories.translates' => function($query) use ($languageId) {
                    $query->where('language_id', $languageId);
                }
            ])
            ->active()
            ->whereHas('categories', function($q) use ($product) {
                $q->whereIn('product_categories.id', $product->categories->pluck('id'));
            })
            ->where('id', '!=', $product->id)
            ->limit(8);
    
            $similarProducts = $similarProductsQuery->get();
    
            $similarProductsData = $similarProducts->map(function ($product) use ($languageId, $user) {
                $translation = $product->translates->first();
                $mainImage = $product->images->where('is_main', true)->first() ?? $product->images->first();
    
                // Category slug və parent slug
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
    
                // --- inCart
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
    
                // --- inFavorite
                $inFavorite = [
                    'isActive' => false,
                    'color_id' => null,
                ];
                if ($user) {
                    $favItem = \App\Models\Favorite::where('user_id', $user->id)
                        ->where('product_id', $product->id)
                        ->first();
                    if ($favItem) {
                        $inFavorite['isActive'] = true;
                        $inFavorite['color_id'] = $favItem->product_color_id ?? null;
                    }
                }
    
                // --- inCompare
                $inCompare = false;
                if ($user) {
                    $inCompare = \App\Models\ProductCompare::where('user_id', $user->id)
                        ->where('product_id', $product->id)
                        ->exists();
                }
    
                return [
                    'id' => $product->id,
                    'title' => $translation ? $translation->title : '',
                    'card_image' => $mainImage && $mainImage->image
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
            'translates' => function($query) use ($languageId, $slug) {
                $query->where('language_id', $languageId)
                      ->where('slug', $slug);
            }
        ])
        ->whereHas('translates', function($query) use ($languageId, $slug) {
            $query->where('language_id', $languageId)
                  ->where('slug', $slug);
        })
        ->firstOrFail();

        $translation = $category->translates->first();
        $data = [
            'id' => $category->id,
            'title' => $translation->title ?? '',
            'slug' => $translation->slug ?? '',
            'seo_title' => $translation->seo_title ?? '',
            'seo_description' => $translation->seo_description ?? '',
            'seo_keywords' => $translation->seo_keywords ?? '',
            'image' => asset('storage/' . $category->image),
            'image_alt_text' => $translation->image_alt_text ?? ''
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

            // Get product with translations and relationships
            $product = Product::with([
                'translates' => function($query) use ($languageId) {
                    $query->where('language_id', $languageId);
                },
                'categories.translates' => function($query) use ($languageId) {
                    $query->where('language_id', $languageId);
                },
                'images' => function($query) use ($languageId) {
                    $query->where('is_main', true)
                          ->where('status', true)
                          ->with(['translates' => function($q) use ($languageId) {
                              $q->where('language_id', $languageId);
                          }]);
                }
            ])
            ->whereHas('translates', function($query) use ($languageId, $slug) {
                $query->where('language_id', $languageId)
                      ->where('slug', $slug);
            })
            ->firstOrFail();

            $translation = $product->translates->first();
            $mainImage = $product->images->first();
            
            // Get category title
            $categoryTitle = '';
            if ($product->categories->isNotEmpty()) {
                $category = $product->categories->first();
                $categoryTranslation = $category->translates->first();
                $categoryTitle = $categoryTranslation ? $categoryTranslation->title : '';
            }

            // Get image alt text from translation
            $imageAltText = '';
            if ($mainImage && $mainImage->translates->isNotEmpty()) {
                $imageAltText = $mainImage->translates->first()->alt_text ?? '';
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
                'discount_price' => $product->discount_price
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
