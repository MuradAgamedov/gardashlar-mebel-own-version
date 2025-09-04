<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
use App\Models\ProductPriceFilter;
use App\Models\ProductColor;
use App\Models\ProductType;
use App\Models\ProductPageCategory;
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
     *                     type="object",
     *                     @OA\Property(property="price_asc", type="string", example="Qiymət artımına görə (ucuzdan bahaya)"),
     *                     @OA\Property(property="price_desc", type="string", example="Qiymət azalmaya görə (bahadan ucuza)"),
     *                     @OA\Property(property="newest", type="string", example="Ən yenilər (ən son əlavə olunan məhsullar əvvəl göstərilir)"),
     *                     @OA\Property(property="oldest", type="string", example="Ən köhnələr (əvvəl əlavə olunan məhsullar əvvəl göstərilir)")
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

            // Get sort options from dictionary
            $sortOptions = Cache::rememberForever("sort_options_{$languageId}", function () use ($languageId) {
                $sortKeywords = [
                    'price_asc' => 'price_asc',
                    'price_desc' => 'price_desc', 
                    'newest' => 'newest',
                    'oldest' => 'oldest'
                ];

                $sortData = [];
                foreach ($sortKeywords as $key => $keyword) {
                    $sortData[$key] = Dictionary::getValue($keyword, $languageId);
                }

                return $sortData;
            });

            return response()->json([
                'success' => true,
                'message' => 'Product categories retrieved successfully',
                'data' => [
                    'categories' => $categoriesData,
                    'product_price_filters' => $priceFiltersData,
                    'product_colors' => $productColorsData,
                    'product_types' => $productTypesData,
                    'product_page_categories' => $productPageCategoriesData,
                    'sort_options' => $sortOptions
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
