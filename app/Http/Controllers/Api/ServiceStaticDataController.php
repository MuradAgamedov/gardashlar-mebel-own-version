<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ServiceStaticData;
use App\Traits\LocaleFromHeader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

/**
 * @OA\Tag(
 *     name="Service Static Data",
 *     description="API Endpoints for Service Static Data"
 * )
 */
class ServiceStaticDataController extends Controller
{
    use LocaleFromHeader;

    /**
     * @OA\Get(
     *     path="/api/service-static-data",
     *     summary="Get service static data",
     *     description="Returns service static data with translated content based on header language",
     *     tags={"Service Static Data"},
     *     @OA\Parameter(
     *         name="locale",
     *         in="header",
     *         description="Language ID (1=Az, 2=En, 3=Ru)",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Service static data retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Service static data retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="title", type="string", example="Xidmətlərimiz"),
     *                 @OA\Property(property="description", type="string", example="Yüksək keyfiyyətli xidmətlərimiz"),
     *                 @OA\Property(property="card_1_image_url", type="string", example="https://example.com/storage/service-static/card1.jpg"),
     *                 @OA\Property(property="card_2_image_url", type="string", example="https://example.com/storage/service-static/card2.jpg"),
     *                 @OA\Property(property="card_3_image_url", type="string", example="https://example.com/storage/service-static/card3.jpg"),
     *                 @OA\Property(property="card_1_image_title", type="string", example="Card 1 Title"),
     *                 @OA\Property(property="card_1_image_alt_text", type="string", example="Card 1 Alt Text"),
     *                 @OA\Property(property="card_2_image_title", type="string", example="Card 2 Title"),
     *                 @OA\Property(property="card_2_image_alt_text", type="string", example="Card 2 Alt Text"),
     *                 @OA\Property(property="card_3_image_title", type="string", example="Card 3 Title"),
     *                 @OA\Property(property="card_3_image_alt_text", type="string", example="Card 3 Alt Text")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Service static data not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Service static data not found")
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        $languageId = $this->getLanguageIdFromHeader($request);
        
        $cacheKey = "service_static_data_{$languageId}";
        
        return Cache::remember($cacheKey, 3600, function () use ($languageId) {
            $serviceStaticData = ServiceStaticData::first();
            
            if (!$serviceStaticData) {
                return response()->json([
                    'success' => false,
                    'message' => 'Service static data not found'
                ], 404);
            }
            
            $translation = $serviceStaticData->translates()
                ->where('language_id', $languageId)
                ->first();
            
            if (!$translation) {
                return response()->json([
                    'success' => false,
                    'message' => 'Translation not found for the specified language'
                ], 404);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Service static data retrieved successfully',
                'data' => [
                    'id' => $serviceStaticData->id,
                    'title' => $translation->title,
                    'description' => $translation->description,
                    'card_1_image_url' => $serviceStaticData->card_1_image ? asset('storage/' . $serviceStaticData->card_1_image) : null,
                    'card_2_image_url' => $serviceStaticData->card_2_image ? asset('storage/' . $serviceStaticData->card_2_image) : null,
                    'card_3_image_url' => $serviceStaticData->card_3_image ? asset('storage/' . $serviceStaticData->card_3_image) : null,
                    'card_1_image_title' => $translation->card_1_image_title,
                    'card_1_image_alt_text' => $translation->card_1_image_alt_text,
                    'card_2_image_title' => $translation->card_2_image_title,
                    'card_2_image_alt_text' => $translation->card_2_image_alt_text,
                    'card_3_image_title' => $translation->card_3_image_title,
                    'card_3_image_alt_text' => $translation->card_3_image_alt_text,
                ]
            ]);
        });
    }
} 