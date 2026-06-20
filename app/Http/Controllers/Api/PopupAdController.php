<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Interfaces\Services\Front\PopupAdServiceInterface;
use App\Http\Resources\Api\PopupAdResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use App\Traits\LocaleFromHeader;

class PopupAdController extends Controller
{
    use LocaleFromHeader;

    protected PopupAdServiceInterface $popupAdService;

    public function __construct(PopupAdServiceInterface $popupAdService)
    {
        $this->popupAdService = $popupAdService;
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $languageId = $this->getLanguageIdFromHeader($request);
            $cacheKey = "api_popup_ads_{$languageId}";

            $popupAds = Cache::rememberForever($cacheKey, function () use ($languageId) {
                return $this->popupAdService->getActiveOrderedWithTranslations($languageId, [
                    'translations' => fn($q) => $q->where('language_id', $languageId)
                ]);
            });

            return response()->json([
                'success' => true,
                'data' => PopupAdResource::collection($popupAds),
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving popup ads: ' . $e->getMessage(),
                'data' => [],
            ], 500);
        }
    }
}
