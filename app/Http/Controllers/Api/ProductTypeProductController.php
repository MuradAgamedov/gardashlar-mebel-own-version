<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductType;
use App\Models\HomePageTypeProduct;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use App\Traits\LocaleFromHeader;
use OpenApi\Annotations as OA;

class ProductTypeProductController extends Controller
{
    use LocaleFromHeader;

    /**
     * @OA\Get(
     *     path="/api/product-types/{productTypeId}/products",
     *     operationId="getProductsByType",
     *     tags={"Product Type Products"},
     *     summary="Get products for a specific product type",
     *     description="Returns products that belong to a specific product type, with homepage status",
     *     @OA\Parameter(
     *         name="productTypeId",
     *         in="path",
     *         description="The ID of the product type",
     *         required=true,
     *         @OA\Schema(type="integer", example=2)
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
     *     @OA\Parameter(
     *         name="homepage_only",
     *         in="query",
     *         description="Return only products that are on homepage for this type",
     *         required=false,
     *         @OA\Schema(type="boolean", example=true)
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
     *                     @OA\Property(property="description", type="string", example="Product description"),
     *                     @OA\Property(property="slug", type="string", example="product-name")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product type not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Product type not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Error retrieving products")
     *         )
     *     )
     * )
     */
    public function index(Request $request, $productTypeId): JsonResponse
    {
        try {
            $languageId = $this->getLanguageIdFromHeader($request);
            $homepageOnly = $request->boolean('homepage_only', false);
            
            // Check if product type exists
            $productType = ProductType::where('id', $productTypeId)
                ->where('status', true)
                ->first();
                
            if (!$productType) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product type not found'
                ], 404);
            }
            
            $cacheKey = "api:product-type:{$productTypeId}:products:lang:{$languageId}:homepage:" . ($homepageOnly ? 'true' : 'false');
            
            $products = Cache::rememberForever($cacheKey, function () use ($productTypeId, $languageId, $homepageOnly) {
                $query = Product::whereHas('types', function($q) use ($productTypeId) {
                    $q->where('product_types.id', $productTypeId);
                })->where('status', true);
                
                if ($homepageOnly) {
                    $query->whereHas('homePageTypeProducts', function($q) use ($productTypeId) {
                        $q->where('product_type_id', $productTypeId)
                          ->where('status', true);
                    });
                }
                
                return $query->with(['translates' => function($q) use ($languageId) {
                    $q->where('language_id', $languageId);
                }])
                ->orderBy('order')
                ->get()
                ->map(function ($product) use ($productTypeId, $languageId) {
                    $translation = $product->translates->first();
                    $homePageEntry = $product->homePageTypeProducts()
                        ->where('product_type_id', $productTypeId)
                        ->first();
                    
                    return [
                        'id' => $product->id,
                        'title' => $translation ? $translation->title : 'No title',
                        'description' => $translation ? $translation->description : null,
                        'slug' => $translation ? $translation->slug : null,
                    ];
                });
            });
            
            return response()->json([
                'success' => true,
                'data' => $products
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving products: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }
} 