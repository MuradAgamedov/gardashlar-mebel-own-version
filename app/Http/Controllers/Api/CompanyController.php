<?php

namespace App\Http\Controllers\Api;

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

    /**
     * @OA\Get(
     *     path="/api/companies",
     *     operationId="getCompanies",
     *     tags={"Companies"},
     *     summary="Get all companies",
     *     description="Returns a list of all active companies with translations based on locale header",
     *     @OA\Parameter(
     *         name="locale",
     *         in="header",
     *         description="Language ID (e.g., 1 for Azerbaijani, 2 for English, 3 for Russian)",
     *         required=false,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Companies retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Companies retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="card_image_url", type="string", example="https://example.com/storage/companies/card_image.jpg", nullable=true),
     *                     @OA\Property(property="card_image_alt_text", type="string", example="Card Image", nullable=true)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Error retrieving companies")
     *         )
     *     )
     * )
     */
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

    /**
     * @OA\Get(
     *     path="/api/companies/{id}",
     *     operationId="getCompany",
     *     tags={"Companies"},
     *     summary="Get company details with products",
     *     description="Returns company details including inner image, title and associated products",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Company ID",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="locale",
     *         in="header",
     *         description="Language ID (e.g., 1 for Azerbaijani, 2 for English, 3 for Russian)",
     *         required=false,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Company retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Company retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="title", type="string", example="Company Title"),
     *                 @OA\Property(property="inner_image", type="string", example="https://example.com/storage/companies/inner_image.jpg", nullable=true),
     *                 @OA\Property(property="inner_image_alt_text", type="string", example="Inner Image", nullable=true),
     *                 @OA\Property(
     *                     property="products",
     *                     type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="title", type="string", example="Product Title"),
     *                         @OA\Property(property="slug", type="string", example="product-slug"),
     *                         @OA\Property(property="price", type="number", format="float", example=99.99),
     *                         @OA\Property(property="view_count", type="integer", example=150),
     *                         @OA\Property(property="card_image", type="string", example="https://example.com/storage/products/image.jpg"),
     *                         @OA\Property(property="discount_percent", type="number", format="float", example=10.5),
     *                         @OA\Property(property="category_slug", type="string", example="category-slug", nullable=true),
     *                         @OA\Property(property="parentSlug", type="string", example="parent-category-slug", nullable=true),
     *                         @OA\Property(
     *                             property="product_types",
     *                             type="array",
     *                             @OA\Items(
     *                                 @OA\Property(property="id", type="integer", example=1),
     *                                 @OA\Property(property="title", type="string", example="Product Type")
     *                             )
     *                         )
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Company not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Company not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Error retrieving company")
     *         )
     *     )
     * )
     */
    public function show(Request $request, $slug)
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
    
            // Get company with products and cache (slug-a görə)
            $company = Cache::remember("company_{$slug}_{$languageId}", 3600, function () use ($slug, $languageId) {
                return Company::with([
                    'translates' => function($query) use ($languageId) {
                        $query->where('language_id', $languageId);
                    },
                    'companyProducts.product' => function($query) use ($languageId) {
                        $query->with([
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
                        ]);
                    }
                ])
                ->active()
                ->whereHas('translates', function($q) use ($slug, $languageId) {
                    $q->where('language_id', $languageId)
                      ->where('slug', $slug);
                })
                ->first();
            });
    
            if (!$company) {
                return response()->json([
                    'success' => false,
                    'message' => 'Company not found'
                ], 404);
            }
    
            $translation = $company->translates->first();
    
            // Products map hissəsi sənin kodundan dəyişməz qaldı
            $products = $company->companyProducts->map(function ($companyProduct) use ($languageId) {
                $product = $companyProduct->product;
                $productTranslation = $product->translates->first();
    
                $mainImage = $product->images->where('is_main', true)->first() ?? $product->images->first();
    
                $categorySlug = null;
                $parentSlug = null;
    
                if ($product->categories->isNotEmpty()) {
                    $category = $product->categories->first();
                    $categoryTranslation = $category->translates->first();
                    $categorySlug = $categoryTranslation ? $categoryTranslation->slug : null;
    
                    if ($category->parent_id) {
                        $parentCategory = \App\Models\ProductCategory::find($category->parent_id);
                        if ($parentCategory) {
                            $parentTranslation = $parentCategory->translates()
                                ->where('language_id', $languageId)
                                ->first();
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
                    'title' => $productTranslation ? $productTranslation->title : '',
                    'slug' => $productTranslation ? $productTranslation->slug : '',
                    'price' => $product->price,
                    'view_count' => $product->view_count,
                    'card_image' => $mainImage && $mainImage->image
                        ? asset('storage/' . $mainImage->image)
                        : asset('images/default_image.webp'),
                    'discount_percent' => $product->discount_percent,
                    'category_slug' => $categorySlug,
                    'parentSlug' => $parentSlug,
                    'product_types' => $productTypes
                ];
            });
    
            $companyData = [
                'title' => $translation ? $translation->title : '',
                'inner_image' => $translation && $translation->inner_image
                    ? asset('storage/' . $translation->inner_image)
                    : null,
                'inner_image_alt_text' => $translation ? $translation->inner_image_alt_text : null,
                'products' => $products
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
