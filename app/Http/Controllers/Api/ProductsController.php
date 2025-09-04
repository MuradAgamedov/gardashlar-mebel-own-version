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
     *         name="category_id",
     *         in="query",
     *         description="Filter products by category ID. If parent category, returns products from all children + parent. If child category, returns only products from that category.",
     *         required=false,
     *         @OA\Schema(type="integer", example=5)
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
     *                     @OA\Property(
     *                         property="children",
     *                         type="array",
     *                         @OA\Items(
     *                             @OA\Property(property="id", type="integer", example=2),
     *                             @OA\Property(property="title", type="string", example="Alt Kategori 1"),
     *                             @OA\Property(property="slug", type="string", example="alt-kategori-1"),
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
     *                         @OA\Property(property="card_image", type="string", example="https://admin.gardaslar.coder.az/storage/products/... or https://admin.gardaslar.coder.az/images/default_image.webp"),
     *                         @OA\Property(property="price", type="number", format="float", example=299.99),
     *                         @OA\Property(property="discount_percent", type="number", format="float", example=10.00),
     *                         @OA\Property(property="view_count", type="integer", example=150),
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
            $languageId = $request->header('locale', 1);
            $language = Language::find($languageId);
            
            if (!$language) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid language ID'
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
            $categoryId = $request->get('category_id');

            // Build products query
            $productsQuery = Product::with([
                'translates' => function($query) use ($languageId) {
                    $query->where('language_id', $languageId);
                },
                'images' => function($query) {
                    $query->where('status', true)->orderBy('order');
                },
                'types.translates' => function($query) use ($languageId) {
                    $query->where('language_id', $languageId);
                }
            ])
            ->active();

            // Filter by category if category_id is provided
            if ($categoryId) {
                // Get the category
                $category = ProductCategory::find($categoryId);
                
                if ($category) {
                    // Check if it's a parent category (has children)
                    $children = ProductCategory::where('parent_id', $categoryId)->pluck('id');
                    
                    if ($children->isNotEmpty()) {
                        // If parent category, get products from all children + parent itself
                        $categoryIds = $children->push($categoryId)->toArray();
                    } else {
                        // If child category, get products only from this category
                        $categoryIds = [$categoryId];
                    }
                    
                    // Filter products that belong to any of these categories
                    $productsQuery->whereHas('categories', function($query) use ($categoryIds) {
                        $query->whereIn('product_categories.id', $categoryIds);
                    });
                }
            }

            // Get products with pagination
            $products = $productsQuery->orderBy('view_count', 'desc')
                ->paginate($perPage, ['*'], 'page', $page);

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
                    'card_image' => $mainImage && $mainImage->image ? asset('storage/' . $mainImage->image) : asset('images/default_image.webp'),
                    'price' => $product->price,
                    'discount_percent' => $product->discount_percent,
                    'view_count' => $product->view_count,
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
                    'pagination' => $paginationData
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
}
