<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
use App\Models\ProductPriceFilter;
use App\Models\ProductColor;
use App\Models\ProductType;
use App\Models\ProductPageCategory;
use App\Models\Product;
use App\Models\ProductFavorite;
use App\Models\ProductCompare;
use App\Models\Dictionary;
use App\Models\Language;
use App\Traits\LocaleFromHeader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

/**
 * @OA\Tag(
 *     name="Products",
 *     description="API Endpoints for products and product categories"
 * )
 * 
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 */
class ProductsController extends Controller
{
    use LocaleFromHeader;

    /**
     * @OA\Get(
     *     path="/api/products",
     *     summary="Get all product categories with hierarchical structure",
     *     tags={"Products"},
     *     @OA\Parameter(
     *         name="locale",
     *         in="header",
     *         description="Language ID (1=az, 2=en, 3=ru)",
     *         required=false,
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         required=false,
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of products per page",
     *         required=false,
     *         @OA\Schema(type="integer", default=12)
     *     ),
     *     @OA\Parameter(
     *         name="category_slug",
     *         in="query",
     *         description="Filter products by category slug. If parent category, returns products from all children + parent. If child category, returns only products from that category.",
     *         required=false,
     *         @OA\Schema(type="string", example="masalar")
     *     ),
     *     @OA\Parameter(
     *         name="sort",
     *         in="query",
     *         description="Sort products by: price_asc, price_desc, newest, oldest, view_count",
     *         required=false,
     *         @OA\Schema(type="string", example="price_asc")
     *     ),
     *     @OA\Parameter(
     *         name="type_id",
     *         in="query",
     *         description="Filter products by type IDs (can be single value or multiple: type_id=1&type_id=2)",
     *         required=false,
     *         @OA\Schema(type="array", @OA\Items(type="integer"), example={1, 2, 3})
     *     ),
     *     @OA\Parameter(
     *         name="filters",
     *         in="query",
     *         description="Price filter IDs (can be single value or multiple: filters=1&filters=2)",
     *         required=false,
     *         @OA\Schema(type="array", @OA\Items(type="integer"), example={1, 2, 3})
     *     ),
     *     @OA\Parameter(
     *         name="color",
     *         in="query",
     *         description="Filter products by color IDs (can be single value or multiple: color=1&color=2) - products with images of these colors",
     *         required=false,
     *         @OA\Schema(type="array", @OA\Items(type="integer"), example={1, 2, 3})
     *     ),
     *     @OA\Parameter(
     *         name="min_price",
     *         in="query",
     *         description="Minimum price filter (direct price parameter)",
     *         required=false,
     *         @OA\Schema(type="number", format="float", example=100.00)
     *     ),
     *     @OA\Parameter(
     *         name="max_price",
     *         in="query",
     *         description="Maximum price filter (direct price parameter)",
     *         required=false,
     *         @OA\Schema(type="number", format="float", example=500.00)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Product categories retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="categories",
     *                     type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="title", type="string", example="Ana Kategori"),
     *                         @OA\Property(property="slug", type="string", example="ana-kategori"),
     *                         @OA\Property(property="seo_title", type="string", example="Ana Kategori - Gardashlar"),
     *                         @OA\Property(property="seo_keywords", type="string", example="ana kategori, gardashlar, mebel"),
     *                         @OA\Property(property="seo_description", type="string", example="Ana kategori haqqında təsvir"),
     *                         @OA\Property(property="parentSlug", type="string", example=null, description="Parent category slug if parent_id exists, null otherwise"),
     *                         @OA\Property(
     *                             property="children",
     *                             type="array",
     *                             @OA\Items(
     *                                 @OA\Property(property="id", type="integer", example=2),
     *                                 @OA\Property(property="title", type="string", example="Alt Kategori 1"),
     *                                 @OA\Property(property="slug", type="string", example="alt-kategori-1"),
     *                                 @OA\Property(property="seo_title", type="string", example="Alt Kategori 1 - Gardashlar"),
     *                                 @OA\Property(property="seo_keywords", type="string", example="alt kategori, gardashlar"),
     *                                 @OA\Property(property="seo_description", type="string", example="Alt kategori haqqında təsvir"),
     *                                 @OA\Property(property="parentSlug", type="string", example="ana-kategori", description="Parent category slug if parent_id exists, null otherwise"),
     *                                 @OA\Property(property="children", type="array", @OA\Items())
     *                             )
     *                         )
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="product_price_filters",
     *                     type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="title", type="string", example="0-100 AZN"),
     *                         @OA\Property(property="min_price", type="number", format="float", example=0.00),
     *                         @OA\Property(property="max_price", type="number", format="float", example=100.00)
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="product_colors",
     *                     type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="color_code", type="string", example="#FF0000")
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="product_types",
     *                     type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="title", type="string", example="Masa")
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="product_page_categories",
     *                     type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="title", type="string", example="Ofis"),
     *                         @OA\Property(property="slug", type="string", example="ofis"),
     *                         @OA\Property(property="parentSlug", type="string", example=null, description="Parent category slug if parent_id exists, null otherwise")
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="sort_options",
     *                     type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="value", type="string", example="price_asc"),
     *                         @OA\Property(property="label", type="string", example="price_asc")
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="sort_translations",
     *                     type="object",
     *                     @OA\Property(property="price_asc", type="string", example="Qiymət artımına görə (ucuzdan bahaya)"),
     *                     @OA\Property(property="price_desc", type="string", example="Qiymət azalmaya görə (bahadan ucuza)"),
     *                     @OA\Property(property="newest", type="string", example="Ən yenilər (ən son əlavə olunan məhsullar əvvəl göstərilir)"),
     *                     @OA\Property(property="oldest", type="string", example="Ən köhnələr (əvvəl əlavə olunan məhsullar əvvəl göstərilir)"),
     *                     @OA\Property(property="view_count", type="string", example="Ən çox baxılanlar")
     *                 ),
     *                 @OA\Property(
     *                     property="products",
     *                     type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="title", type="string", example="Masa"),
     *                         @OA\Property(property="slug", type="string", example="masa-1"),
     *                         @OA\Property(property="price", type="number", format="float", example=299.99),
     *                         @OA\Property(property="view_count", type="integer", example=150),
     *                         @OA\Property(property="card_image", type="string", example="https://admin.gardaslar.coder.az/storage/products/... or https://admin.gardaslar.coder.az/images/default_image.webp"),
     *                         @OA\Property(property="discount_percent", type="number", format="float", example=10.00),
     *                         @OA\Property(property="category_slug", type="string", example="masalar", description="Product category slug, null if no category"),
     *                         @OA\Property(property="parentSlug", type="string", example="mebel", description="Parent category slug if category has parent, null otherwise"),
     *                         @OA\Property(
     *                             property="product_types",
     *                             type="array",
     *                             @OA\Items(
     *                                 @OA\Property(property="id", type="integer", example=1),
     *                                 @OA\Property(property="title", type="string", example="Masa")
     *                             )
     *                         )
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="pagination",
     *                     type="object",
     *                     @OA\Property(property="current_page", type="integer", example=1),
     *                     @OA\Property(property="per_page", type="integer", example=12),
     *                     @OA\Property(property="total", type="integer", example=100),
     *                     @OA\Property(property="last_page", type="integer", example=9),
     *                     @OA\Property(property="from", type="integer", example=1),
     *                     @OA\Property(property="to", type="integer", example=12),
     *                     @OA\Property(property="has_more_pages", type="boolean", example=true)
     *                 ),
     *                 @OA\Property(
     *                     property="applied_filters",
     *                     type="object",
     *                     @OA\Property(property="min_price", type="number", format="float", example=100.00),
     *                     @OA\Property(property="max_price", type="number", format="float", example=500.00)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid language ID",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Invalid language ID")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Error retrieving product categories")
     *         )
     *     )
     * )
     */
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
                    // Get the first category (assuming products have one primary category)
                    $category = $product->categories->first();
                    $categoryTranslation = $category->translates->first();
                    $categorySlug = $categoryTranslation ? $categoryTranslation->slug : null;
                    
                    // Get parent slug if category has parent
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
                
                return [
                    'id' => $product->id,
                    'title' => $translation ? $translation->title : '',
                    'slug' => $translation ? $translation->slug : '',
                    'price' => $product->price,
                    'view_count' => $product->view_count,
                    'card_image' => $mainImage && $mainImage->image ? asset('storage/' . $mainImage->image) : asset('images/default_image.webp'),
                    'discount_percent' => $product->discount_percent,
                    'category_slug' => $categorySlug,
                    'parentSlug' => $parentSlug,
                    'product_types' => $productTypes
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
    
            // Find product by slug (any language)
            $product = Product::with([
                'translates' => function($query) use ($languageId) {
                    $query->where('language_id', $languageId);
                },
                'types.translates' => function($query) use ($languageId) {
                    $query->where('language_id', $languageId);
                },
                'tags.translates' => function($query) use ($languageId) {
                    $query->where('language_id', $languageId);
                },
                'accordions.translates' => function($query) use ($languageId) {
                    $query->where('language_id', $languageId);
                },
                'details' => function($query) {
                    $query->orderBy('order');
                },
                'details.translates' => function($query) use ($languageId) {
                    $query->where('language_id', $languageId);
                },
                'detailValues' => function($query) {
                    $query->orderBy('order');
                },
                'detailValues.translates' => function($query) use ($languageId) {
                    $query->where('language_id', $languageId);
                },
                'detailValues.details' => function($query) {
                    $query->orderBy('order');
                },
                'detailValues.details.translates' => function($query) use ($languageId) {
                    $query->where('language_id', $languageId);
                },
                'categories.translates' => function($query) use ($languageId) {
                    $query->where('language_id', $languageId);
                },
                'images' => function($query) {
                    $query->where('status', true)->orderBy('order');
                }
            ])->whereHas('translates', function($query) use ($slug) {
                $query->where('slug', $slug);
            })->first();
    
            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found'
                ], 404);
            }
    
            // --- Product detallı data (sənin əvvəlki show kodundakı kimi) ---
            $translation = $product->translates->first();
            $productTypes = $product->types->map(function ($type) {
                $translation = $type->translates->first();
                return [
                    'id' => $type->id,
                    'title' => $translation ? $translation->title : ''
                ];
            });
            $productTags = $product->tags->map(function ($tag) {
                $translation = $tag->translates->first();
                return [
                    'id' => $tag->id,
                    'title' => $translation ? $translation->title : ''
                ];
            });
            $productAccordions = $product->accordions->map(function ($accordion) {
                $translation = $accordion->translates->first();
                return [
                    'id' => $accordion->id,
                    'title' => $translation ? $translation->title : '',
                    'content' => $translation ? $translation->description : ''
                ];
            });
            $productDetails = $product->details->map(function ($detail) {
                $translation = $detail->translates->first();
                return [
                    'id' => $detail->id,
                    'keyword' => $translation ? $translation->keyword : '',
                    'value' => $translation ? $translation->value : ''
                ];
            })->filter(fn($d) => $d['keyword'] && $d['value'])->values();
            $productDetailValues = $product->detailValues->map(function ($detailValue) use ($languageId) {
                $translation = $detailValue->translates->first();
                $details = $detailValue->details()
                    ->with(['translates' => function($query) use ($languageId) {
                        $query->where('language_id', $languageId);
                    }])
                    ->orderBy('order')
                    ->get()
                    ->map(function ($detail) {
                        $detailTranslation = $detail->translates->first();
                        return [
                            'id' => $detail->id,
                            'keyword' => $detailTranslation ? $detailTranslation->keyword : '',
                            'value' => $detailTranslation ? $detailTranslation->value : ''
                        ];
                    })
                    ->filter(fn($d) => $d['keyword'] && $d['value'])
                    ->values();
                return [
                    'id' => $detailValue->id,
                    'keyword' => $translation ? $translation->keyword : '',
                    'details' => $details
                ];
            });
    
            // Şəkillər (color filter ilə)
            $images = $product->images;
            $availableColors = $images->where('product_color_id', '!=', null)
                ->pluck('product_color_id')->unique()->values();
            if ($colorId) {
                $images = $images->where('product_color_id', $colorId);
            }
            $productImages = $images->map(function ($image) {
                $data = [
                    'id' => $image->id,
                    'image_url' => $image->image ? asset('storage/' . $image->image) : null,
                    'is_main' => $image->is_main,
                    'order' => $image->order
                ];
                if ($image->product_color_id !== null) {
                    $data['color_id'] = $image->product_color_id;
                }
                return $data;
            });
    
            // Discount
            $discountPrice = null;
            if ($product->discount_percent && $product->discount_percent > 0) {
                $discountPrice = $product->price - ($product->price * $product->discount_percent / 100);
            }
    
            // Available colors
            $availableColorDetails = [];
            if ($availableColors->isNotEmpty()) {
                $colors = ProductColor::whereIn('id', $availableColors)->get();
                $availableColorDetails = $colors->map(fn($c) => [
                    'id' => $c->id,
                    'color_code' => $c->color_code
                ]);
            }
    
            // Categories (parent_slug və parent_title ilə)
            $productCategories = $product->categories->map(function ($category) use ($languageId) {
                $tr = $category->translates->first();
                $parentSlug = null;
                $parentTitle = null;
                if ($category->parent_id) {
                    $parentCategory = ProductCategory::find($category->parent_id);
                    if ($parentCategory) {
                        $parentTr = $parentCategory->translates()->where('language_id', $languageId)->first();
                        $parentSlug = $parentTr ? $parentTr->slug : null;
                        $parentTitle = $parentTr ? $parentTr->title : null;
                    }
                }
                return [
                    'id' => $category->id,
                    'title' => $tr ? $tr->title : '',
                    'slug' => $tr ? $tr->slug : '',
                    'parent_slug' => $parentSlug,
                    'parent_title' => $parentTitle
                ];
            });
    
            // Product əsas data
            $productData = [
                'id' => $product->id,
                'title' => $translation ? $translation->title : '',
                'slug' => $translation ? $translation->slug : '',
                'price' => $product->price,
                'discount_price' => $discountPrice,
                'discount_percent' => $product->discount_percent,
                'view_count' => $product->view_count,
                'product_types' => $productTypes,
                'product_tags' => $productTags,
                'product_accordions' => $productAccordions,
                'product_details' => $productDetails,
                'product_detail_values' => $productDetailValues,
                'available_colors' => $availableColorDetails,
                'images' => $productImages,
                'categories' => $productCategories
            ];
    
            // --- Similar məhsullar (eyni kategoriyadan) ---
            $similar = Product::whereHas('categories', function($q) use ($product) {
                    $q->whereIn('product_categories.id', $product->categories->pluck('id'));
                })
                ->where('id', '!=', $product->id)
                ->active()
                ->take(8)
                ->get();
    
            $similarData = $similar->map(function ($item) use ($languageId) {
                return $item->similarData($languageId);
            });
    
            return response()->json([
                'success' => true,
                'message' => 'Product retrieved successfully',
                'data' => [
                    'product' => $productData,
                    'similar_products' => $similarData
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

            $product = Product::findOrFail($id);
            $details = $product->details()->with(['translates' => function($query) use ($languageId) {
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
                'message' => 'Product details retrieved successfully',
                'data' => $detailsData
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
     * @OA\Post(
     *     path="/api/products/{id}/compare",
     *     operationId="addProductToCompare",
     *     tags={"Products"},
     *     summary="Add product to compare list",
     *     description="Add a product to user's compare list (max 4 products)",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Product ID",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(
     *             @OA\Property(property="color_id", type="integer", example=1, description="Product color ID (optional)")
     *         )
     *     ),
     *     @OA\SecurityRequirement(name="bearerAuth"),
     *     @OA\Response(
     *         response=200,
     *         description="Product added to compare list successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Məhsul müqayisə siyahısına əlavə edildi"),
     *             @OA\Property(property="data", type="object", 
     *                 properties={
     *                     @OA\Property(property="is_compared", type="boolean", example=true),
     *                     @OA\Property(property="compare_count", type="integer", example=2),
     *                     @OA\Property(property="color_id", type="integer", example=1, nullable=true)
     *                 }
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Maximum compare limit reached",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Müqayisə siyahısında maksimum 4 məhsul ola bilər"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Məhsul tapılmadı"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     )
     * )
     */
    public function addToCompare(Request $request, int $id)
    {
        try {
            $userId = $request->user()->id;
            $colorId = $request->input('color_id');

            // Check if product exists
            $product = Product::active()->find($id);
            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Məhsul tapılmadı',
                    'data' => null
                ], 404);
            }

            // Validate color_id if provided
            if ($colorId && !ProductColor::find($colorId)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Rəng tapılmadı',
                    'data' => null
                ], 404);
            }

            // Check if already in compare list
            if (ProductCompare::isCompared($userId, $id, $colorId)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Məhsul artıq müqayisə siyahısındadır',
                    'data' => null
                ], 400);
            }

            // Check compare list limit (max 4 products)
            $compareCount = ProductCompare::getCompareCount($userId);
            if ($compareCount >= 4) {
                return response()->json([
                    'success' => false,
                    'message' => 'Müqayisə siyahısında maksimum 4 məhsul ola bilər',
                    'data' => null
                ], 400);
            }

            // Add to compare
            ProductCompare::addToCompare($userId, $id, $colorId);

            return response()->json([
                'success' => true,
                'message' => 'Məhsul müqayisə siyahısına əlavə edildi',
                'data' => [
                    'is_compared' => true,
                    'compare_count' => $compareCount + 1,
                    'color_id' => $colorId
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Məhsul müqayisə siyahısına əlavə edilərkən xəta baş verdi: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/products/{id}/compare",
     *     operationId="removeProductFromCompare",
     *     tags={"Products"},
     *     summary="Remove product from compare list",
     *     description="Remove a product from user's compare list",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Product ID",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(
     *             @OA\Property(property="color_id", type="integer", example=1, description="Product color ID (optional)")
     *         )
     *     ),
     *     @OA\SecurityRequirement(name="bearerAuth"),
     *     @OA\Response(
     *         response=200,
     *         description="Product removed from compare list successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Məhsul müqayisə siyahısından silindi"),
     *             @OA\Property(property="data", type="object", 
     *                 properties={
     *                     @OA\Property(property="is_compared", type="boolean", example=false),
     *                     @OA\Property(property="compare_count", type="integer", example=1),
     *                     @OA\Property(property="color_id", type="integer", example=1, nullable=true)
     *                 }
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Məhsul tapılmadı"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     )
     * )
     */
    public function removeFromCompare(Request $request, int $id)
    {
        try {
            $userId = $request->user()->id;
            $colorId = $request->input('color_id');

            // Check if product exists
            $product = Product::active()->find($id);
            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Məhsul tapılmadı',
                    'data' => null
                ], 404);
            }

            // Remove from compare
            ProductCompare::removeFromCompare($userId, $id, $colorId);

            $compareCount = ProductCompare::getCompareCount($userId);

            return response()->json([
                'success' => true,
                'message' => 'Məhsul müqayisə siyahısından silindi',
                'data' => [
                    'is_compared' => false,
                    'compare_count' => $compareCount,
                    'color_id' => $colorId
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Məhsul müqayisə siyahısından silinərkən xəta baş verdi: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/products/compare",
     *     operationId="getUserCompareList",
     *     tags={"Products"},
     *     summary="Get user's compare list",
     *     description="Returns list of user's products in compare list",
     *     @OA\SecurityRequirement(name="bearerAuth"),
     *     @OA\Parameter(
     *         name="locale",
     *         in="header",
     *         description="Language code (az, en, ru)",
     *         required=false,
     *         @OA\Schema(type="string", example="az")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object", 
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="title", type="string", example="Modern Masa"),
     *                 @OA\Property(property="slug", type="string", example="modern-masa"),
     *                 @OA\Property(property="price", type="number", example=299.99),
     *                 @OA\Property(property="discount_percent", type="number", example=10),
     *                 @OA\Property(property="main_image", type="string", example="https://example.com/storage/products/image.jpg"),
     *                 @OA\Property(property="description", type="string", example="Yüksək keyfiyyətli masa"),
     *                 @OA\Property(property="categories", type="array", @OA\Items(type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="title", type="string", example="Masalar")
     *                 )),
     *                 @OA\Property(property="types", type="array", @OA\Items(type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="title", type="string", example="Yemək masası")
     *                 )),
     *                 @OA\Property(property="color_id", type="integer", example=1, nullable=true),
     *                 @OA\Property(property="color", type="object", nullable=true,
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="color_code", type="string", example="#FF0000")
     *                 )
     *             )),
     *             @OA\Property(property="compare_count", type="integer", example=3)
     *         )
     *     )
     * )
     */
    public function getCompareList(Request $request)
    {
        try {
            $userId = $request->user()->id;

            $languageId = $this->getLanguageIdFromHeader($request);
            $cacheKey = "api:products:compare:user:{$userId}:lang:{$languageId}";
            
            $data = Cache::rememberForever($cacheKey, function () use ($userId, $languageId) {
                // Get user's compare list
                $compares = ProductCompare::with([
                    'product.translates' => function($query) use ($languageId) {
                        $query->where('language_id', $languageId);
                    },
                    'product.categories.translates' => function($query) use ($languageId) {
                        $query->where('language_id', $languageId);
                    },
                    'product.types.translates' => function($query) use ($languageId) {
                        $query->where('language_id', $languageId);
                    },
                    'product.images' => function($query) {
                        $query->where('status', true)->orderBy('order');
                    },
                    'color'
                ])
                ->where('user_id', $userId)
                ->whereHas('product', function($query) {
                    $query->active();
                })
                ->orderBy('created_at', 'desc')
                ->get();

                // Transform the data
                $transformedData = $compares->map(function ($compare) use ($languageId) {
                    $product = $compare->product;
                    
                    // Get translation for the specified language
                    $translation = $product->translates()
                        ->where('language_id', $languageId)
                        ->first();
                    
                    // If no translation found, get the first available translation
                    if (!$translation) {
                        $translation = $product->translates()->first();
                    }

                    // Get main image
                    $mainImage = $product->images()->where('status', true)->first();

                    // Get categories
                    $categories = $product->categories->map(function ($category) use ($languageId) {
                        $categoryTranslation = $category->translates()
                            ->where('language_id', $languageId)
                            ->first();
                        
                        if (!$categoryTranslation) {
                            $categoryTranslation = $category->translates()->first();
                        }

                        return [
                            'id' => $category->id,
                            'title' => $categoryTranslation ? $categoryTranslation->title : null
                        ];
                    });

                    // Get types
                    $types = $product->types->map(function ($type) use ($languageId) {
                        $typeTranslation = $type->translates()
                            ->where('language_id', $languageId)
                            ->first();
                        
                        if (!$typeTranslation) {
                            $typeTranslation = $type->translates()->first();
                        }

                        return [
                            'id' => $type->id,
                            'title' => $typeTranslation ? $typeTranslation->title : null
                        ];
                    });

                    return [
                        'id' => $product->id,
                        'title' => $translation ? $translation->title : null,
                        'slug' => $translation ? $translation->slug : null,
                        'price' => $product->price,
                        'discount_percent' => $product->discount_percent,
                        'main_image' => $mainImage ? $mainImage->image_url : asset('images/default_image.webp'),
                        'description' => $translation ? $translation->description : null,
                        'categories' => $categories,
                        'types' => $types,
                        'color_id' => $compare->color_id,
                        'color' => $compare->color ? [
                            'id' => $compare->color->id,
                            'color_code' => $compare->color->color_code
                        ] : null
                    ];
                })->filter(function ($item) {
                    // Filter out items without title or slug
                    return !empty($item['title']) && !empty($item['slug']);
                })->values();

                return [
                    'data' => $transformedData,
                    'compare_count' => $compares->count()
                ];
            });

            return response()->json([
                'success' => true, 
                'data' => $data['data'], 
                'compare_count' => $data['compare_count']
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Müqayisə siyahısı əldə edilərkən xəta baş verdi: ' . $e->getMessage(),
                'data' => [],
                'compare_count' => 0
            ], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/products/compare/clear",
     *     operationId="clearUserCompareList",
     *     tags={"Products"},
     *     summary="Clear user's compare list",
     *     description="Remove all products from user's compare list",
     *     @OA\SecurityRequirement(name="bearerAuth"),
     *     @OA\Response(
     *         response=200,
     *         description="Compare list cleared successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Müqayisə siyahısı təmizləndi"),
     *             @OA\Property(property="data", type="object", 
     *                 properties={
     *                     @OA\Property(property="compare_count", type="integer", example=0)
     *                 }
     *             )
     *         )
     *     )
     * )
     */
    public function clearCompareList(Request $request)
    {
        try {
            $userId = $request->user()->id;

            // Clear compare list
            ProductCompare::clearUserCompareList($userId);

            return response()->json([
                'success' => true,
                'message' => 'Müqayisə siyahısı təmizləndi',
                'data' => [
                    'compare_count' => 0
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Müqayisə siyahısı təmizlənərkən xəta baş verdi: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/products/favorites/check",
     *     operationId="checkFavoriteProducts",
     *     tags={"Products"},
     *     summary="Check if products exist (for non-registered users)",
     *     description="Check if provided product IDs exist and are active",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="product_ids", type="array", @OA\Items(type="integer"), example={1, 2, 3, 4}, description="Array of product IDs to check")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="existing_products", type="array", @OA\Items(type="integer"), example={1, 2, 3}),
     *                 @OA\Property(property="non_existing_products", type="array", @OA\Items(type="integer"), example={4}),
     *                 @OA\Property(property="total_requested", type="integer", example=4),
     *                 @OA\Property(property="total_existing", type="integer", example=3),
     *                 @OA\Property(property="total_non_existing", type="integer", example=1)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid request",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validation error"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     )
     * )
     */
    public function checkFavoriteProducts(Request $request)
    {
        try {
            // Validate request
            $request->validate([
                'product_ids' => 'required|array|min:1|max:50',
                'product_ids.*' => 'required|integer|min:1'
            ]);

            $productIds = $request->input('product_ids', []);
            
            // Get language ID from header using trait
            $languageId = $this->getLanguageIdFromHeader($request);
            
            // Get existing active products with all relationships
            $existingProducts = Product::active()
                ->whereIn('id', $productIds)
                ->with([
                    'translates' => function($query) use ($languageId) {
                        $query->where('language_id', $languageId);
                    },
                    'images' => function($query) {
                        $query->where('status', true)->orderBy('order');
                    },
                    'images.color' => function($query) {
                        $query->select('product_colors.id', 'product_colors.color_code');
                    },
                    'categories.translates' => function($query) use ($languageId) {
                        $query->where('language_id', $languageId);
                    },
                    'types.translates' => function($query) use ($languageId) {
                        $query->where('language_id', $languageId);
                    },
                    'colors' => function($query) {
                        $query->select('product_colors.id', 'product_colors.color_code');
                    }
                ])
                ->get();
            
            // Find non-existing products
            $existingProductIds = $existingProducts->pluck('id')->toArray();
            $nonExistingProducts = array_diff($productIds, $existingProductIds);
            
            // Format existing products data
            $productsData = $existingProducts->map(function ($product) use ($languageId) {
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
                    // Get the first category (assuming products have one primary category)
                    $category = $product->categories->first();
                    $categoryTranslation = $category->translates->first();
                    $categorySlug = $categoryTranslation ? $categoryTranslation->slug : null;
                    
                    // Get parent slug if category has parent
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
                
                // Get available colors for this product
                $availableColors = $product->colors->map(function($color) {
                    return [
                        'id' => $color->id,
                        'color_code' => $color->color_code
                    ];
                });
                
                // Get all images with their colors
                $imagesWithColors = $product->images->map(function($image) {
                    return [
                        'id' => $image->id,
                        'image_url' => $image->image ? asset('storage/' . $image->image) : null,
                        'is_main' => $image->is_main,
                        'order' => $image->order,
                        'color' => $image->color ? [
                            'id' => $image->color->id,
                            'color_code' => $image->color->color_code
                        ] : null
                    ];
                })->sortBy('order')->values();
                
                return [
                    'id' => $product->id,
                    'title' => $translation ? $translation->title : '',
                    'slug' => $translation ? $translation->slug : '',
                    'price' => $product->price,
                    'view_count' => $product->view_count,
                    'discount_percent' => $product->discount_percent,
                    'category_slug' => $categorySlug,
                    'parentSlug' => $parentSlug,
                    'product_types' => $productTypes,
                    'available_colors' => $availableColors,
                    'images' => $imagesWithColors
                ];
            });
            
            return response()->json([
                'success' => true,
                'data' => $productsData
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error: ' . implode(', ', $e->errors()['product_ids'] ?? []),
                'data' => null
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Məhsulları yoxlayarkən xəta baş verdi: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/products/card/dont-registered",
     *     operationId="checkCardProducts",
     *     tags={"Products"},
     *     summary="Check if products exist for card (for non-registered users)",
     *     description="Check if provided product IDs exist and are active for card functionality",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="product_ids", type="array", @OA\Items(type="integer"), example={1, 2, 3, 4}, description="Array of product IDs to check")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="title", type="string", example="Product Title"),
     *                 @OA\Property(property="slug", type="string", example="product-slug"),
     *                 @OA\Property(property="price", type="number", example=99.99),
     *                 @OA\Property(property="view_count", type="integer", example=150),
     *                 @OA\Property(property="discount_percent", type="integer", example=10),
     *                 @OA\Property(property="category_slug", type="string", example="category-slug"),
     *                 @OA\Property(property="parentSlug", type="string", example="parent-category-slug"),
     *                 @OA\Property(property="product_types", type="array", @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="title", type="string", example="Type Title")
     *                 )),
     *                 @OA\Property(property="available_colors", type="array", @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="color_code", type="string", example="#FF0000")
     *                 )),
     *                 @OA\Property(property="images", type="array", @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="image_url", type="string", example="http://example.com/image.jpg"),
     *                     @OA\Property(property="is_main", type="boolean", example=true),
     *                     @OA\Property(property="order", type="integer", example=1),
     *                     @OA\Property(property="color", type="object", nullable=true)
     *                 ))
     *             ))
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid request",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validation error"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     )
     * )
     */
    public function checkCardProducts(Request $request)
    {
        try {
            // Validate request
            $request->validate([
                'product_ids' => 'required|array|min:1|max:50',
                'product_ids.*' => 'required|integer|min:1'
            ]);

            $productIds = $request->input('product_ids', []);
            
            // Get language ID from header using trait
            $languageId = $this->getLanguageIdFromHeader($request);
            
            // Get existing active products with all relationships
            $existingProducts = Product::active()
                ->whereIn('id', $productIds)
                ->with([
                    'translates' => function($query) use ($languageId) {
                        $query->where('language_id', $languageId);
                    },
                    'images' => function($query) {
                        $query->where('status', true)->orderBy('order');
                    },
                    'images.color' => function($query) {
                        $query->select('product_colors.id', 'product_colors.color_code');
                    },
                    'categories.translates' => function($query) use ($languageId) {
                        $query->where('language_id', $languageId);
                    },
                    'types.translates' => function($query) use ($languageId) {
                        $query->where('language_id', $languageId);
                    },
                    'colors' => function($query) {
                        $query->select('product_colors.id', 'product_colors.color_code');
                    }
                ])
                ->get();
            
            // Find non-existing products
            $existingProductIds = $existingProducts->pluck('id')->toArray();
            $nonExistingProducts = array_diff($productIds, $existingProductIds);
            
            // Format existing products data
            $productsData = $existingProducts->map(function ($product) use ($languageId) {
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
                    // Get the first category (assuming products have one primary category)
                    $category = $product->categories->first();
                    $categoryTranslation = $category->translates->first();
                    $categorySlug = $categoryTranslation ? $categoryTranslation->slug : null;
                    
                    // Get parent slug if category has parent
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
                
                // Get available colors for this product
                $availableColors = $product->colors->map(function($color) {
                    return [
                        'id' => $color->id,
                        'color_code' => $color->color_code
                    ];
                });
                
                // Get all images with their colors
                $imagesWithColors = $product->images->map(function($image) {
                    return [
                        'id' => $image->id,
                        'image_url' => $image->image ? asset('storage/' . $image->image) : null,
                        'is_main' => $image->is_main,
                        'order' => $image->order,
                        'color' => $image->color ? [
                            'id' => $image->color->id,
                            'color_code' => $image->color->color_code
                        ] : null
                    ];
                })->sortBy('order')->values();
                
                return [
                    'id' => $product->id,
                    'title' => $translation ? $translation->title : '',
                    'slug' => $translation ? $translation->slug : '',
                    'price' => $product->price,
                    'view_count' => $product->view_count,
                    'discount_percent' => $product->discount_percent,
                    'category_slug' => $categorySlug,
                    'parentSlug' => $parentSlug,
                    'product_types' => $productTypes,
                    'available_colors' => $availableColors,
                    'images' => $imagesWithColors
                ];
            });
            
            return response()->json([
                'success' => true,
                'data' => $productsData
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error: ' . implode(', ', $e->errors()['product_ids'] ?? []),
                'data' => null
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Məhsulları yoxlayarkən xəta baş verdi: ' . $e->getMessage(),
                'data' => null
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
