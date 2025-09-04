<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
use App\Models\ProductPriceFilter;
use App\Models\ProductColor;
use App\Models\ProductType;
use App\Models\ProductPageCategory;
use App\Models\Product;
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
     *         description="Filter products by type IDs (array)",
     *         required=false,
     *         @OA\Schema(type="array", @OA\Items(type="integer"), example={1, 2, 3})
     *     ),
     *     @OA\Parameter(
     *         name="filters",
     *         in="query",
     *         description="Price filter IDs (array)",
     *         required=false,
     *         @OA\Schema(type="array", @OA\Items(type="integer"), example={1, 2, 3})
     *     ),
     *     @OA\Parameter(
     *         name="color",
     *         in="query",
     *         description="Filter products by color IDs (array) - products with images of these colors",
     *         required=false,
     *         @OA\Schema(type="array", @OA\Items(type="integer"), example={1, 2, 3})
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
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="title", type="string", example="Ana Kategori"),
     *                     @OA\Property(property="slug", type="string", example="ana-kategori"),
     *                     @OA\Property(property="seo_title", type="string", example="Ana Kategori - Gardashlar"),
     *                     @OA\Property(property="seo_keywords", type="string", example="ana kategori, gardashlar, mebel"),
     *                     @OA\Property(property="seo_description", type="string", example="Ana kategori haqqında təsvir"),
     *                     @OA\Property(
     *                         property="children",
     *                         type="array",
     *                         @OA\Items(
     *                             @OA\Property(property="id", type="integer", example=2),
     *                             @OA\Property(property="title", type="string", example="Alt Kategori 1"),
     *                             @OA\Property(property="slug", type="string", example="alt-kategori-1"),
     *                             @OA\Property(property="seo_title", type="string", example="Alt Kategori 1 - Gardashlar"),
     *                             @OA\Property(property="seo_keywords", type="string", example="alt kategori, gardashlar"),
     *                             @OA\Property(property="seo_description", type="string", example="Alt kategori haqqında təsvir"),
     *                             @OA\Property(property="children", type="array", @OA\Items())
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
     *                         @OA\Property(property="slug", type="string", example="ofis")
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

            $productPageCategoriesData = $productPageCategories->map(function ($pageCategory) {
                $category = $pageCategory->productCategory;
                $translation = $category ? $category->translates->first() : null;
                return [
                    'id' => $pageCategory->id,
                    'title' => $translation ? $translation->title : '',
                    'slug' => $translation ? $translation->slug : ''
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

            // Build products query
            $productsQuery = Product::with([
                'translates' => function($query) use ($languageId) {
                    $query->where('language_id', $languageId);
                },
                'images' => function($query) use ($colorIds) {
                    $query->where('status', true)->orderBy('order');
                    if (!empty($colorIds) && is_array($colorIds)) {
                        $query->whereIn('color_id', $colorIds);
                    }
                },
                'types.translates' => function($query) use ($languageId) {
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
                    $query->whereIn('color_id', $colorIds)->where('status', true);
                });
            }

            // Filter by price range using filter IDs
            $appliedMinPrice = null;
            $appliedMaxPrice = null;
            
            if (!empty($filterIds) && is_array($filterIds)) {
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

            $productsData = $products->map(function ($product) {
                $translation = $product->translates->first();
                // Find main image - first try to find is_main=true, then fallback to first image
                $mainImage = $product->images->where('is_main', true)->first();
                if (!$mainImage) {
                    $mainImage = $product->images->first();
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
                        'min_price' => $appliedMinPrice,
                        'max_price' => $appliedMaxPrice
                    ]
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
            $categoryData = [
                'id' => $category->id,
                'title' => $translation ? $translation->title : '',
                'slug' => $translation ? $translation->slug : '',
                'seo_title' => $translation ? $translation->seo_title : '',
                'seo_keywords' => $translation ? $translation->seo_keywords : '',
                'seo_description' => $translation ? $translation->seo_description : '',
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
     *         description="Filter images by color ID. If not provided, shows images from the first available color.",
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

            // Get translation for the requested language
            $translation = $product->translates->first();

            // Get product types
            $productTypes = $product->types->map(function ($type) {
                $translation = $type->translates->first();
                return [
                    'id' => $type->id,
                    'title' => $translation ? $translation->title : ''
                ];
            });

            // Get product tags
            $productTags = $product->tags->map(function ($tag) {
                $translation = $tag->translates->first();
                return [
                    'id' => $tag->id,
                    'title' => $translation ? $translation->title : ''
                ];
            });

            // Get product accordions
            $productAccordions = $product->accordions->map(function ($accordion) {
                $translation = $accordion->translates->first();
                return [
                    'id' => $accordion->id,
                    'title' => $translation ? $translation->title : '',
                    'content' => $translation ? $translation->description : ''
                ];
            });

            // Get product images with color filtering
            $images = $product->images;
            
            // If no color_id provided, get the first available color
            if (!$colorId) {
                $firstColorId = $images->where('color_id', '!=', null)->min('color_id');
                if ($firstColorId) {
                    $colorId = $firstColorId;
                }
            }
            
            // Filter images by color_id if specified
            if ($colorId) {
                $images = $images->where('color_id', $colorId);
            }
            
            $productImages = $images->map(function ($image) {
                $imageData = [
                    'id' => $image->id,
                    'image_url' => $image->image ? asset('storage/' . $image->image) : null,
                    'is_main' => $image->is_main,
                    'order' => $image->order
                ];
                
                // Only include color_id if it's not null
                if ($image->color_id !== null) {
                    $imageData['color_id'] = $image->color_id;
                }
                
                return $imageData;
            });

            // Calculate discount price
            $discountPrice = null;
            if ($product->discount_percent && $product->discount_percent > 0) {
                $discountPrice = $product->price - ($product->price * $product->discount_percent / 100);
            }

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
                'images' => $productImages
            ];

            return response()->json([
                'success' => true,
                'message' => 'Product retrieved successfully',
                'data' => $productData
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
}
