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

            return $this->success(PopupAdResource::collection($popupAds), 'Popup ads retrieved successfully');

        } catch (\Exception $e) {
            return $this->serverError('Error retrieving popup ads: ' . $e->getMessage());
        }
    }
}
