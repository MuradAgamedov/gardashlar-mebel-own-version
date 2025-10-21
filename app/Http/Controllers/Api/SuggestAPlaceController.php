<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SuggestAPlace;
use App\Traits\LocaleFromHeader;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(name="Suggest A Place", description="API Endpoints for Suggest A Place")
 */
class SuggestAPlaceController extends Controller
{
    use LocaleFromHeader;
   
    public function index(Request $request): JsonResponse
    {
        try {
            $languageId = $this->getLanguageIdFromHeader($request);
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
