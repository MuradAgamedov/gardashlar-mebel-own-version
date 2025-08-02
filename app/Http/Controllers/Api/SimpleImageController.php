<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SimpleImage;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use App\Traits\LocaleFromHeader;
use OpenApi\Annotations as OA;

class SimpleImageController extends Controller
{
    use LocaleFromHeader;

    /**
     * @OA\Get(
     *     path="/api/simple-images",
     *     operationId="getSimpleImages",
     *     tags={"Simple Images"},
     *     summary="Get all simple images",
     *     description="Returns a list of all simple images with their keywords and URLs",
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
     *                     @OA\Property(property="keyword", type="string", example="hero_image"),
     *                     @OA\Property(property="image_url", type="string", example="http://example.com/storage/simple-images/hero_image.jpg")
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
     *             @OA\Property(property="message", type="string", example="Error retrieving images")
     *         )
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $images = Cache::rememberForever('api_simple_images', function () {
                return SimpleImage::select(['id', 'keyword', 'image_path'])
                    ->get()
                    ->map(function ($image) {
                        return [
                            'id' => $image->id,
                            'keyword' => $image->keyword,
                            'image_url' => $image->image_url
                        ];
                    });
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

    /**
     * @OA\Get(
     *     path="/api/simple-images/{keyword}",
     *     operationId="getSimpleImageByKeyword",
     *     tags={"Simple Images"},
     *     summary="Get simple image by keyword",
     *     description="Returns a simple image by its keyword",
     *     @OA\Parameter(
     *         name="keyword",
     *         in="path",
     *         required=true,
     *         description="Image keyword",
     *         @OA\Schema(type="string", example="hero_image")
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
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="keyword", type="string", example="hero_image"),
     *                 @OA\Property(property="image_url", type="string", example="http://example.com/storage/simple-images/hero_image.jpg")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Image not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Image not found"),
     *             @OA\Property(property="data", type="object", nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Error retrieving image")
     *         )
     *     )
     * )
     */
    public function show(Request $request, string $keyword): JsonResponse
    {
        try {
            $image = SimpleImage::where('keyword', $keyword)->first();

            if (!$image) {
                return response()->json([
                    'success' => false,
                    'message' => 'Image not found',
                    'data' => null
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $image->id,
                    'keyword' => $image->keyword,
                    'image_url' => $image->image_url
                ]
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
     * @OA\Get(
     *     path="/api/simple-images/keywords/{keywords}",
     *     operationId="getSimpleImagesByKeywords",
     *     tags={"Simple Images"},
     *     summary="Get multiple simple images by keywords",
     *     description="Returns multiple simple images by comma-separated keywords",
     *     @OA\Parameter(
     *         name="keywords",
     *         in="path",
     *         required=true,
     *         description="Comma-separated image keywords",
     *         @OA\Schema(type="string", example="hero_image,logo,banner")
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
     *                     @OA\Property(property="keyword", type="string", example="hero_image"),
     *                     @OA\Property(property="image_url", type="string", example="http://example.com/storage/simple-images/hero_image.jpg")
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
     *             @OA\Property(property="message", type="string", example="Error retrieving images")
     *         )
     *     )
     * )
     */
    public function getByKeywords(Request $request, string $keywords): JsonResponse
    {
        try {
            $keywordArray = array_map('trim', explode(',', $keywords));
            
            $images = SimpleImage::whereIn('keyword', $keywordArray)
                ->get()
                ->mapWithKeys(function ($image) {
                    return [$image->keyword => $image->image_url];
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
