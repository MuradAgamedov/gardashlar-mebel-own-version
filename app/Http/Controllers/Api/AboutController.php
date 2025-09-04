<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\About;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use App\Traits\LocaleFromHeader;
use OpenApi\Annotations as OA;

class AboutController extends Controller
{
    use LocaleFromHeader;

    /**
     * @OA\Get(
     *     path="/api/abouts",
     *     operationId="getAbouts",
     *     tags={"Abouts"},
     *     summary="Get all about information",
     *     description="Returns a list of all active about information, translated based on locale header",
     *     @OA\Parameter(
     *         name="locale",
     *         in="header",
     *         description="Language ID (e.g., 1 for Azerbaijani, 2 for English)",
     *         required=false,
     *         @OA\Schema(type="integer", example=1)
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
     *                     @OA\Property(property="image_url", type="string", example="https://example.com/storage/abouts/image.jpg", nullable=true),
     *                     @OA\Property(property="date", type="string", example="2024", nullable=true),
     *                     @OA\Property(property="title", type="string", example="About Us"),
     *                     @OA\Property(property="description", type="string", example="We are a leading company..."),
     *                     @OA\Property(property="image_alt_text", type="string", example="About Us Image", nullable=true)
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
     *             @OA\Property(property="message", type="string", example="Error retrieving about information")
     *         )
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        try {
            // Get language ID from header using trait
            $languageId = $this->getLanguageIdFromHeader($request);
            
            $cacheKey = "api_abouts_{$languageId}";
            
            $abouts = Cache::rememberForever($cacheKey, function () use ($languageId) {
                return About::with(['translates' => function($query) use ($languageId) {
                    $query->where('language_id', $languageId);
                }])
                ->where('status', true)
                ->orderBy('order')
                ->get()
                ->map(function ($about) {
                    $translation = $about->translates->first();
                    
                    return [
                        'id' => $about->id,
                        'image_url' => $about->image ? asset('storage/' . $about->image) : asset('images/default_image.webp'),
                        'date' => $about->date,
                        'title' => $translation ? $translation->title : '',
                        'description' => $translation ? $translation->description : '',
                        'image_alt_text' => $translation ? $translation->image_alt_text : null
                    ];
                });
            });

            return response()->json([
                'success' => true,
                'data' => $abouts
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving about information: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }
} 