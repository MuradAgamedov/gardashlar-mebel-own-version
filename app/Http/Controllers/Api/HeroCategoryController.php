<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HeroCategory;
use App\Models\HeroProduct;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use App\Traits\LocaleFromHeader;
use OpenApi\Annotations as OA;

class HeroCategoryController extends Controller
{
    use LocaleFromHeader;

    /**
     * @OA\Get(
     *     path="/api/hero-categories",
     *     operationId="getHeroCategories",
     *     tags={"HeroCategories"},
     *     summary="Get all active hero categories",
     *     description="Returns a list of active hero categories with their product category details, ordered by 'order' field",
     *     @OA\Parameter(
     *         name="locale",
     *         in="header",
     *         description="Language_id from languages api",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *             example=1
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="title", type="string", example="Electronics")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Error retrieving hero categories")
     *         )
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $languageId = $this->getLanguageIdFromHeader($request);
            $cacheKey = "api:hero-categories:lang:{$languageId}";

            $heroCategories = Cache::rememberForever($cacheKey, function () use ($languageId) {
                return HeroCategory::active()
                    ->ordered()
                    ->with(['productCategory.translates' => fn($q) => $q->where('language_id', $languageId)])
                    ->get()
                    ->map(function ($heroCategory) {
                        $productCategory = $heroCategory->productCategory;
                        $translate = $productCategory->translates->first();
                        
                        return [
                            'id' => $productCategory->id,
                            'title' => $translate->title ?? null,
                        ];
                    });
            });

            return response()->json([
                'success' => true,
                'data' => $heroCategories
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving hero categories: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/hero-categories/{categoryId}/products",
     *     operationId="getHeroProductsByCategory",
     *     tags={"HeroCategories"},
     *     summary="Get hero products by category ID",
     *     description="Returns hero products for a specific category with product details including main image, name, slug, price, and discount_percent",
     *     @OA\Parameter(
     *         name="categoryId",
     *         in="path",
     *         description="Product category ID",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             example=1
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="locale",
     *         in="header",
     *         description="Language_id from languages api",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *             example=1
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="title", type="string", example="Product Name"),
     *                     @OA\Property(property="slug", type="string", example="product-name"),
     *                     @OA\Property(property="price", type="number", example=99.99),
     *                     @OA\Property(property="discount_percent", type="number", example=10.5),
     *                     @OA\Property(property="main_image_url", type="string", example="https://example.com/image.jpg")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Category not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Category not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Error retrieving hero products")
     *         )
     *     )
     * )
     */
    public function getProductsByCategory(Request $request, int $categoryId): JsonResponse
    {
        try {
            $languageId = $this->getLanguageIdFromHeader($request);
            $cacheKey = "api:hero-products:category:{$categoryId}:lang:{$languageId}";

            $heroProducts = Cache::rememberForever($cacheKey, function () use ($categoryId, $languageId) {
                return HeroProduct::active()
                    ->ordered()
                    ->with([
                        'product.translates' => fn($q) => $q->where('language_id', $languageId),
                        'product.images' => fn($q) => $q->where('is_main', true)->active(),
                        'product.categories' => fn($q) => $q->where('product_categories.id', $categoryId)
                    ])
                    ->get()
                    ->filter(function ($heroProduct) use ($categoryId) {
                        // Filter products that belong to the specified category
                        return $heroProduct->product->categories->contains('id', $categoryId);
                    })
                    ->map(function ($heroProduct) {
                        $product = $heroProduct->product;
                        $translate = $product->translates->first();
                        $mainImage = $product->images->first();
                        
                        return [
                            'id' => $product->id,
                            'title' => $translate->title ?? null,
                            'slug' => $translate->slug ?? null,
                            'price' => $product->price ?? null,
                            'discount_percent' => $product->discount_percent ?? 0,
                            'main_image_url' => $mainImage ? $mainImage->image_url : null,
                        ];
                    })
                    ->values() // Reset array keys and convert to array
                    ->toArray(); // Convert Collection to array
            });

            return response()->json([
                'success' => true,
                'data' => $heroProducts
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving hero products: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }
} 