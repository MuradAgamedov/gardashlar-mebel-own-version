<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SuggestAPlace;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(name="Suggest A Place", description="API Endpoints for Suggest A Place")
 */
class SuggestAPlaceController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/suggest-a-place",
     *     operationId="getSuggestAPlace",
     *     tags={"Suggest A Place"},
     *     summary="Get suggest a place data",
     *     description="Returns suggest a place information",
     *     @OA\Parameter(name="locale", in="header", description="Language ID (1=En, 3=Az)", required=true, @OA\Schema(type="integer", example=3)),
     *     @OA\Response(response=200, description="Successful response", @OA\JsonContent(type="object", @OA\Property(property="success", type="boolean", example=true), @OA\Property(property="message", type="string", example="Suggest a place data retrieved successfully"), @OA\Property(property="data", type="object", @OA\Property(property="id", type="integer", example=1), @OA\Property(property="title", type="string", example="We Chose Your Place For You!"), @OA\Property(property="description", type="string", example="Do you need the perfect venue for your project to launch successfully? We offer you the ideal venue with its location, amenities, and infrastructure!")))),
     *     @OA\Response(response=404, description="Suggest a place data not found", @OA\JsonContent(type="object", @OA\Property(property="success", type="boolean", example=false), @OA\Property(property="message", type="string", example="Suggest a place data not found"), @OA\Property(property="data", type="object"))),
     *     @OA\Response(response=500, description="Internal server error", @OA\JsonContent(type="object", @OA\Property(property="success", type="boolean", example=false), @OA\Property(property="message", type="string", example="Error retrieving suggest a place data"), @OA\Property(property="data", type="object")))
     * )
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $languageId = $request->header('locale', 3);
            $cacheKey = "api_suggest_a_place_{$languageId}";
            
            $data = Cache::rememberForever($cacheKey, function () use ($languageId) {
                $suggestAPlace = SuggestAPlace::first();
                
                if (!$suggestAPlace) {
                    return null;
                }
                
                $translation = $suggestAPlace->translates()->where('language_id', $languageId)->first();
                
                return [
                    'id' => $suggestAPlace->id,
                    'title' => $translation ? $translation->title : '',
                    'description' => $translation ? $translation->description : '',
                ];
            });
            
            if (!$data) {
                return response()->json([
                    'success' => false,
                    'message' => 'Suggest a place data not found',
                    'data' => null
                ], 404);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Suggest a place data retrieved successfully',
                'data' => $data
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving suggest a place data: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }
}
