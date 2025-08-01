<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HomePageDiscount;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use App\Traits\LocaleFromHeader;
use OpenApi\Annotations as OA;

class HomePageDiscountController extends Controller
{
    use LocaleFromHeader;

    /**
     * @OA\Get(
     *     path="/api/home-page-discount",
     *     operationId="getHomePageDiscount",
     *     tags={"Home Page Sections"},
     *     summary="Get Home Page Discount data",
     *     description="Returns Home Page Discount content with background image and translations",
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
     *                 type="object",
     *                 description="Home Page Discount content",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="bg_image_url", type="string", nullable=true, example="https://example.com/bg-image.jpg"),
     *                 @OA\Property(property="title", type="string", nullable=true, example="Special Discount"),
     *                 @OA\Property(property="subtitle", type="string", nullable=true, example="Get up to 50% off"),
     *                 @OA\Property(property="button_link", type="string", nullable=true, example="https://example.com/shop")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Error retrieving Home Page Discount data")
     *         )
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $languageId = $this->getLanguageIdFromHeader($request);
            $cacheKey = "api:home-page-discount:lang:{$languageId}";
            
            $data = Cache::rememberForever($cacheKey, function () use ($languageId) {
                // Get Home Page Discount data (only first record)
                $homePageDiscount = HomePageDiscount::first();
                
                if (!$homePageDiscount) {
                    return null;
                }
                
                // Get translation for the specified language
                $translation = $homePageDiscount->translates()
                    ->where('language_id', $languageId)
                    ->first();
                
                // Get background image URL
                $bgImageUrl = null;
                if ($homePageDiscount->bg_image) {
                    $imageService = app(\App\Services\ImageService::class);
                    $bgImageUrl = $imageService->getUrl($homePageDiscount->bg_image);
                }
                
                return [
                    'id' => $homePageDiscount->id,
                    'bg_image_url' => $bgImageUrl,
                    'title' => $translation ? $translation->title : null,
                    'subtitle' => $translation ? $translation->subtitle : null,
                    'button_link' => $translation ? $translation->button_link : null
                ];
            });

            if (!$data) {
                return response()->json([
                    'success' => false,
                    'message' => 'Home Page Discount data not found',
                    'data' => null
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $data
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving Home Page Discount data: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }
} 