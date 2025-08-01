<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProductType;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use App\Traits\LocaleFromHeader;
use OpenApi\Annotations as OA;

class ProductTypeController extends Controller
{
    use LocaleFromHeader;

    /**
     * @OA\Get(
     *     path="/api/product-types",
     *     operationId="getProductTypes",
     *     tags={"Məhsul Növləri"},
     *     summary="Get all active product types",
     *     description="Returns a list of active product types with translations (id and title only)",
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
     *             @OA\Property(property="message", type="string", example="Error retrieving product types")
     *         )
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $languageId = $this->getLanguageIdFromHeader($request);
            $cacheKey = "api:product-types:lang:{$languageId}";
            
            $productTypes = Cache::rememberForever($cacheKey, function () use ($languageId) {
                return ProductType::where('status', true)
                    ->orderBy('order')
                    ->get()
                    ->map(function ($type) use ($languageId) {
                        $translation = $type->translate($languageId);
                        return [
                            'id' => $type->id,
                            'title' => $translation ? $translation->title : 'No title'
                        ];
                    });
            });

            return response()->json([
                'success' => true,
                'data' => $productTypes
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving product types: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }
} 