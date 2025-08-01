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
     *     tags={"Məhsul Növləri"},
     *     summary="Müəyyən məhsul növü üçün məhsulları əldə et",
     *     description="Müəyyən məhsul növünə aid olan məhsulları qaytarır, ana səhifə statusu ilə",
     *     @OA\Parameter(
     *         name="productTypeId",
     *         in="path",
     *         description="Məhsul növünün ID-si",
     *         required=true,
     *         @OA\Schema(type="integer", example=2)
     *     ),
     *     @OA\Parameter(
     *         name="locale",
     *         in="header",
     *         description="Dil ID-si (languages API-dən)",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *             example=1
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="homepage_only",
     *         in="query",
     *         description="Yalnız bu növ üçün ana səhifədə olan məhsulları qaytar",
     *         required=false,
     *         @OA\Schema(type="boolean", example=true)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Uğurlu cavab",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1, description="Məhsul ID-si"),
     *                     @OA\Property(property="title", type="string", example="Məhsul Adı", description="Məhsulun başlığı"),
     *                     @OA\Property(property="description", type="string", example="Məhsul təsviri", description="Məhsulun təsviri"),
     *                     @OA\Property(property="slug", type="string", example="mehsul-adi", description="Məhsulun slug-u"),
     *                     @OA\Property(property="price", type="number", example=2500.00, description="Məhsulun qiyməti"),
     *                     @OA\Property(property="view_count", type="integer", example=42, description="Məhsulun baxış sayı"),
     *                     @OA\Property(property="main_image", type="string", nullable=true, example="https://admin.gardaslar.coder.az/uploads/products/image.jpg", description="Əsas şəklin URL-i"),
     *                     @OA\Property(
     *                         property="types",
     *                         type="array",
     *                         description="Məhsul növləri",
     *                         @OA\Items(
     *                             type="object",
     *                             @OA\Property(property="id", type="integer", example=2, description="Növ ID-si"),
     *                             @OA\Property(property="title", type="string", example="Ümumi", description="Növün adı")
     *                         )
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Məhsul növü tapılmadı",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Məhsul növü tapılmadı")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server xətası",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Məhsulları əldə etməkdə xəta baş verdi")
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
                
                return $query->with([
                    'translates' => function($q) use ($languageId) {
                        $q->where('language_id', $languageId);
                    },
                    'types.translates' => function($q) use ($languageId) {
                        $q->where('language_id', $languageId);
                    },
                    'images' => function($q) {
                        $q->active()->ordered();
                    }
                ])
                ->orderBy('order')
                ->get()
                ->map(function ($product) use ($languageId) {
                    $translation = $product->translates->first();
                    
                    // Get main image (is_main = true) or first image
                    $mainImage = $product->images->where('is_main', true)->first() ?? $product->images->first();
                    
                    return [
                        'id' => $product->id,
                        'title' => $translation ? $translation->title : 'No title',
                        'description' => $translation ? $translation->description : null,
                        'slug' => $translation ? $translation->slug : null,
                        'price' => $product->price,
                        'view_count' => $product->view_count ?? 0,
                        'main_image' => $mainImage ? $mainImage->image_url : null,
                        'types' => $product->types->map(function($type) {
                            $typeTranslation = $type->translates->first();
                            return [
                                'id' => $type->id,
                                'title' => $typeTranslation ? $typeTranslation->title : 'No title'
                            ];
                        })
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