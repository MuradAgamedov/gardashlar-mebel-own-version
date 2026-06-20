<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\SimpleImage\GetByKeywordsRequest;
use App\Http\Resources\Api\SimpleImageResource;
use App\Models\SimpleImage;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use App\Traits\LocaleFromHeader;
use OpenApi\Annotations as OA;

class SimpleImageController extends Controller
{
    use LocaleFromHeader;

   
    public function index(Request $request): JsonResponse
    {
        try {
            $languageId = $this->getLanguageIdFromHeader($request);
            $cacheKey = "api:simple_images:all:lang:{$languageId}";

            $images = Cache::rememberForever($cacheKey, function () use ($languageId) {
                return SimpleImage::with(['translates' => fn($q) => $q->where('language_id', $languageId)])
                    ->get();
            });

            return response()->json([
                'success' => true,
                'data' => SimpleImageResource::collection($images)
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving images: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }

    public function show(Request $request, string $keyword): JsonResponse
    {
        try {
            $languageId = $this->getLanguageIdFromHeader($request);
            
            $image = SimpleImage::where('keyword', $keyword)
                ->with(['translates' => fn($q) => $q->where('language_id', $languageId)])
                ->first();

            if (!$image) {
                return response()->json([
                    'success' => false,
                    'message' => 'Image not found',
                    'data' => null
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => new SimpleImageResource($image)
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving image: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/simple-images/by-keywords",
     *     operationId="getSimpleImagesByKeywordsArray",
     *     tags={"Simple Images"},
     *     summary="Get multiple simple images by keywords array",
     *     description="Returns multiple simple images by array of keywords",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="keywords",
     *                 type="array",
     *                 @OA\Items(type="string"),
     *                 example={"hero_image", "logo", "banner"}
     *             )
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
     *                 @OA\Property(property="hero_image", type="string", example="http://example.com/storage/simple-images/hero_image.jpg"),
     *                 @OA\Property(property="logo", type="string", example="http://example.com/storage/simple-images/logo.jpg"),
     *                 @OA\Property(property="banner", type="string", example="http://example.com/storage/simple-images/banner.jpg")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Keywords array is required")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Error retrieving images")
     *         )
     *     )
     * )
     */
    public function getByKeywordsArray(GetByKeywordsRequest $request): JsonResponse
    {
        try {
            $languageId = $this->getLanguageIdFromHeader($request);
            $keywords = $request->input('keywords');
            
            $images = SimpleImage::whereIn('keyword', $keywords)
                ->with(['translates' => fn($q) => $q->where('language_id', $languageId)])
                ->get()
                ->mapWithKeys(function ($image) {
                    $translate = $image->translates->first();
                    return [
                        $image->keyword => [
                            'image_url'      => $image->image_url,
                            'image_go_url'   => $translate?->image_go_url,
                            'image_alt_text' => $translate?->image_alt_text,
                        ]
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $images
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving images: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }
}
