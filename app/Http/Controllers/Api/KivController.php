<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Interfaces\Services\Front\KivCategoryServiceInterface;
use App\Http\Resources\Api\KivCategoryResource;
use App\Traits\LocaleFromHeader;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use OpenApi\Annotations as OA;


class KivController extends Controller
{
    use LocaleFromHeader;

    protected KivCategoryServiceInterface $kivCategoryService;

    public function __construct(KivCategoryServiceInterface $kivCategoryService)
    {
        $this->kivCategoryService = $kivCategoryService;
    }

   
    public function index(Request $request): JsonResponse
    {
        try {
            $languageId = $this->getLanguageIdFromHeader($request);
            $cacheKey = "api_kivs_{$languageId}";
            
            $categories = Cache::rememberForever($cacheKey, function () use ($languageId) {
                return $this->kivCategoryService->getActiveOrderedWithTranslations($languageId, [
                    'kivs' => fn($q) => $q->where('status', true)->orderBy('order')
                ]);
            });

            if ($categories->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No KIVs found',
                    'data' => []
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'KIVs retrieved successfully',
                'data' => KivCategoryResource::collection($categories)
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving KIVs: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }
}

