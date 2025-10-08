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
                
                // Get parent slug if parent_id exists
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

            // Get sort options for frontend select
            $sortOptions = [
                ['value' => 'price_asc', 'label' => 'price_asc'],
                ['value' => 'price_desc', 'label' => 'price_desc'],
                ['value' => 'newest', 'label' => 'newest'],
                ['value' => 'oldest', 'label' => 'oldest'],
                ['value' => 'view_count', 'label' => 'view_count']
            ];

            // Get sort translations from dictionary
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

            // Get pagination parameters
            $perPage = $request->get('per_page', 12);
            $page = $request->get('page', 1);
            $categorySlug = $request->get('category_slug');
            $typeIds = $request->get('type_id', []);
            $filterIds = $request->get('filters', []);
            $colorIds = $request->get('color', []);
            $sort = $request->get('sort', 'view_count');
            $minPrice = $request->get('min_price');
            $maxPrice = $request->get('max_price');
            
            // Get category seo_text if category_slug is provided
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
            
            // Handle filters, colors, and type_id parameters - convert to arrays if they come as single values
            if (!is_array($filterIds)) {
                $filterIds = $filterIds ? [$filterIds] : [];
            }
            if (!is_array($colorIds)) {
                $colorIds = $colorIds ? [$colorIds] : [];
            }
            if (!is_array($typeIds)) {
                $typeIds = $typeIds ? [$typeIds] : [];
            }

            // Build products query
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
                'types.translates' => function($query) use ($languageId) {
                    $query->where('language_id', $languageId);
                },
                'categories.translates' => function($query) use ($languageId) {
                    $query->where('language_id', $languageId);
                }
            ])
            ->active();

            // Filter by category if category_slug is provided
            if ($categorySlug) {
                // Get the category by slug (any language)
                $category = ProductCategory::whereHas('translates', function($query) use ($categorySlug) {
                    $query->where('slug', $categorySlug);
                })->first();
                
                if ($category) {
                    // Check if it's a parent category (has children)
                    $children = ProductCategory::where('parent_id', $category->id)->pluck('id');
                    
                    if ($children->isNotEmpty()) {
                        // If parent category, get products from all children + parent itself
                        $categoryIds = $children->push($category->id)->toArray();
                    } else {
                        // If child category, get products only from this category
                        $categoryIds = [$category->id];
                    }
                    
                    // Filter products that belong to any of these categories
                    $productsQuery->whereHas('categories', function($query) use ($categoryIds) {
                        $query->whereIn('product_categories.id', $categoryIds);
                    });
                }
            }

            // Filter by types if type_ids array is provided
            if (!empty($typeIds) && is_array($typeIds)) {
                // Filter products that belong to any of these types
                $productsQuery->whereHas('types', function($query) use ($typeIds) {
                    $query->whereIn('product_types.id', $typeIds);
                });
            }

            // Filter by colors if color_ids array is provided
            if (!empty($colorIds) && is_array($colorIds)) {
                // Filter products that have images with any of these colors
                $productsQuery->whereHas('images', function($query) use ($colorIds) {
                    $query->whereIn('product_color_id', $colorIds)->where('status', true);
                });
            }

            // Filter by price range using filter IDs or direct price parameters
            $appliedMinPrice = null;
            $appliedMaxPrice = null;
            
            // First check for direct price parameters
            if ($minPrice !== null || $maxPrice !== null) {
                $appliedMinPrice = $minPrice;
                $appliedMaxPrice = $maxPrice;
                
                // Apply direct price range filter
                if ($appliedMinPrice !== null) {
                    $productsQuery->where('price', '>=', $appliedMinPrice);
                }
                
                if ($appliedMaxPrice !== null) {
                    $productsQuery->where('price', '<=', $appliedMaxPrice);
                }
            }
            // If no direct price parameters, check for filter IDs
            elseif (!empty($filterIds) && is_array($filterIds)) {
                // Get price filters by IDs
                $priceFilters = ProductPriceFilter::whereIn('id', $filterIds)->get();
                
                if ($priceFilters->isNotEmpty()) {
                    // Get min and max prices from all selected filters
                    $appliedMinPrice = $priceFilters->min('min_price');
                    $appliedMaxPrice = $priceFilters->max('max_price');
                    
                    // Apply price range filter
                    if ($appliedMinPrice !== null) {
                        $productsQuery->where('price', '>=', $appliedMinPrice);
                    }
                    
                    if ($appliedMaxPrice !== null) {
                        $productsQuery->where('price', '<=', $appliedMaxPrice);
                    }
                }
            }

            // Apply sorting
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
                case 'view_count':
                default:
                    $productsQuery->orderBy('view_count', 'desc');
                    break;
            }

            // Get products with pagination
            $products = $productsQuery->paginate($perPage, ['*'], 'page', $page);

            // Calculate min and max prices from ALL products in the database (not just filtered ones)
            $allProducts = Product::active()->get();
            $actualMinPrice = $allProducts->isNotEmpty() ? $allProducts->min('price') : null;
            $actualMaxPrice = $allProducts->isNotEmpty() ? $allProducts->max('price') : null;

            $productsData = $products->map(function ($product) use ($languageId) {
                $translation = $product->translates->first();
                // Find main image - first try to find is_main=true, then fallback to first image
                $mainImage = $product->images->where('is_main', true)->first();
                if (!$mainImage) {
                    $mainImage = $product->images->first();
                }
                
                // Get category information
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
            
                // ✨ Yeni əlavə: inCart, inFavorite, inCompare
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
            
                    $inCompare = \App\Models\Compare::where('user_id', $user->id)
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
            

            // Pagination data
            $paginationData = [
                'current_page' => $products->currentPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
                'last_page' => $products->lastPage(),
                'from' => $products->firstItem(),
                'to' => $products->lastItem(),
                'has_more_pages' => $products->hasMorePages()
            ];

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
                    'seo_text' => $categorySeoText
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

    /**
     * @OA\Get(
     *     path="/api/products/{slug}",
     *     summary="Get single product by slug",
     *     tags={"Products"},
     *     @OA\Parameter(
     *         name="slug",
     *         in="path",
     *         description="Product slug",
     *         required=true,
     *         @OA\Schema(type="string", example="masa-1")
     *     ),
     *     @OA\Parameter(
     *         name="locale",
     *         in="header",
     *         description="Language code (az, en, ru)",
     *         required=false,
     *         @OA\Schema(type="string", default="az")
     *     ),
     *     @OA\Parameter(
     *         name="color_id",
     *         in="query",
     *         description="Filter images by color ID. If not provided, shows all images for the product.",
     *         required=false,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Product retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="title", type="string", example="Masa"),
     *                 @OA\Property(property="slug", type="string", example="masa-1"),
     *                 @OA\Property(property="price", type="number", format="float", example=299.99),
     *                 @OA\Property(property="discount_price", type="number", format="float", example=269.99, description="Calculated discount price if discount_percent > 0"),
     *                 @OA\Property(property="discount_percent", type="number", format="float", example=10.00),
     *                 @OA\Property(property="view_count", type="integer", example=150),
     *                 @OA\Property(
     *                     property="product_types",
     *                     type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="title", type="string", example="Masa")
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="product_tags",
     *                     type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="title", type="string", example="Premium")
     *                     )
     *                 ),
                      *                 @OA\Property(
                 *                     property="product_accordions",
                 *                     type="array",
                 *                     @OA\Items(
                 *                         @OA\Property(property="id", type="integer", example=1),
                 *                         @OA\Property(property="title", type="string", example="Product Features"),
                 *                         @OA\Property(property="content", type="string", example="Detailed product features and specifications")
                 *                     )
                 *                 ),
                 *                 @OA\Property(
                 *                     property="product_details",
                 *                     type="array",
                 *                     @OA\Items(
                 *                         @OA\Property(property="id", type="integer", example=1),
                 *                         @OA\Property(property="keyword", type="string", example="Material"),
                 *                         @OA\Property(property="value", type="string", example="Wood")
                 *                     )
                 *                 ),
                 *                 @OA\Property(
                 *                     property="product_detail_values",
                 *                     type="array",
                 *                     @OA\Items(
                 *                         @OA\Property(property="id", type="integer", example=1),
                 *                         @OA\Property(property="keyword", type="string", example="Size"),
                 *                         @OA\Property(
                 *                             property="details",
                 *                             type="array",
                 *                             @OA\Items(
                 *                                 @OA\Property(property="id", type="integer", example=1),
                 *                                 @OA\Property(property="keyword", type="string", example="Small"),
                 *                                 @OA\Property(property="value", type="string", example="30x40cm")
                 *                             )
                 *                         )
                 *                     )
                 *                 ),
                 *                 @OA\Property(
                 *                     property="available_colors",
                 *                     type="array",
                 *                     @OA\Items(
                 *                         @OA\Property(property="id", type="integer", example=1),
                 *                         @OA\Property(property="color_code", type="string", example="#FF0000")
                 *                     )
                 *                 ),
                 *                 @OA\Property(
     *                     property="images",
     *                     type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="image_url", type="string", example="https://admin.gardaslar.coder.az/storage/products/image.jpg"),
     *                         @OA\Property(property="is_main", type="boolean", example=true),
     *                         @OA\Property(property="order", type="integer", example=1),
     *                         @OA\Property(property="color_id", type="integer", example=1, description="Only included if not null")
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Product not found")
     *         )
     *     )
     * )
     */
    public function show(Request $request, $slug)
    {
        try {
            $languageId = $this->getLanguageIdFromHeader($request);
            $colorId = $request->get('color_id');
    
            if (!$languageId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid language'
                ], 400);
            }
    
            $product = Product::with([
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
            ])
            ->whereHas('translates', fn($q) => $q->where('slug', $slug))
            ->first();
    
            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found'
                ], 404);
            }
    
            $translation = $product->translates->first();
    
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
                $inCompare = \App\Models\Compare::where('user_id', $user->id)
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
    
            $productImages = $images->map(function ($image) {
                return [
                    'id' => $image->id,
                    'image_url' => $image->image ? asset('storage/' . $image->image) : null,
                    'is_main' => $image->is_main,
                    'order' => $image->order,
                    'color_id' => $image->product_color_id,
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
                    $inCompare = \App\Models\Compare::where('user_id', $user->id)
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
                    'inCart' => $inCart,
                    'inFavorite' => $inFavorite,
                    'inCompare' => $inCompare,
                ];
            });
    
            return response()->json([
                'success' => true,
                'message' => 'Product retrieved successfully',
                'data' => [
                    'product' => $productData,
                    'similar_products' => $similarData,
                ]
            ]);
    
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving product: ' . $e->getMessage()
            ], 500);
        }
    }
    
    

    /**
     * @OA\Get(
     *     path="/api/products/{id}/details",
     *     summary="Get product details",
     *     tags={"Products"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Product ID",
     *         required=true,
     *         @OA\Schema(type="integer", example=45)
     *     ),
     *     @OA\Parameter(
     *         name="locale",
     *         in="header",
     *         description="Language code (az, en, ru)",
     *         required=false,
     *         @OA\Schema(type="string", default="az")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Product details retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="keyword", type="string", example="Material"),
     *                     @OA\Property(property="value", type="string", example="Wood")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
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
                $inCompare = \App\Models\Compare::where('user_id', $user->id)
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
                    $inCompare = \App\Models\Compare::where('user_id', $user->id)
                        ->where('product_id', $product->id)
                        ->exists();
                }
    
                return [
                    'id' => $product->id,
                    'title' => $translation ? $translation->title : '',
                    'slug' => $translation ? $translation->slug : '',
                    'price' => $product->price,
                    'view_count' => $product->view_count,
                    'card_image' => $mainImage && $mainImage->image
                        ? asset('storage/' . $mainImage->image)
                        : asset('images/default_image.webp'),
                    'discount_price' => $product->discount_price,
                    'inCart' => $inCart,
                    'inFavorite' => $inFavorite,
                    'inCompare' => $inCompare,
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
    
    

    /**
     * @OA\Get(
     *     path="/api/products/{id}/detail-values",
     *     summary="Get product detail values",
     *     tags={"Products"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Product ID",
     *         required=true,
     *         @OA\Schema(type="integer", example=45)
     *     ),
     *     @OA\Parameter(
     *         name="locale",
     *         in="header",
     *         description="Language code (az, en, ru)",
     *         required=false,
     *         @OA\Schema(type="string", default="az")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Product detail values retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="keyword", type="string", example="Color Options")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
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

    /**
     * @OA\Get(
     *     path="/api/products/{id}/detail-values/{detailValueId}/details",
     *     summary="Get product detail value details",
     *     tags={"Products"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Product ID",
     *         required=true,
     *         @OA\Schema(type="integer", example=45)
     *     ),
     *     @OA\Parameter(
     *         name="detailValueId",
     *         in="path",
     *         description="Detail Value ID",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="locale",
     *         in="header",
     *         description="Language code (az, en, ru)",
     *         required=false,
     *         @OA\Schema(type="string", default="az")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Product detail value details retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="keyword", type="string", example="Red"),
     *                     @OA\Property(property="value", type="string", example="Bright red color option")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
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

 

 

   

    /**
     * @OA\Get(
     *     path="/api/categories",
     *     summary="Get all product categories with hierarchical structure",
     *     tags={"Products"},
     *     @OA\Parameter(
     *         name="locale",
     *         in="header",
     *         description="Language ID (1=az, 2=en, 3=ru)",
     *         required=false,
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Categories retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Categories retrieved successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="categories", type="array", @OA\Items(type="object"))
     *             )
     *         )
     *     )
     * )
     */
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
}
