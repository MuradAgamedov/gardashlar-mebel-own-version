<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PopupAd;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use App\Traits\LocaleFromHeader;

class PopupAdController extends Controller
{
    use LocaleFromHeader;

    /**
     * GET /api/popup-ads
     */
    public function index(Request $request): JsonResponse
    {
        try {
            // Dil ID-ni header-dən götür
            $languageId = $this->getLanguageIdFromHeader($request);

            // Cache key
            $cacheKey = "api_popup_ads_{$languageId}";

            $popupAds = Cache::rememberForever($cacheKey, function () use ($languageId) {
                return PopupAd::with(['translations' => function ($query) use ($languageId) {
                        $query->where('language_id', $languageId);
                    }])
                    ->where('status', true)
                    ->orderBy('order')
                    ->get()
                    ->map(function ($popup) {
                        $tr = $popup->translations->first();

                        return [
                            'id'            => $popup->id,
                            'image_url'     => $popup->image 
                                                ? asset('storage/' . $popup->image)
                                                : asset('images/default_image.webp'),

                            'title'         => $tr?->title ?? '',
                            'description'   => $tr?->description ?? '',
                            'image_alt_text'=> $tr?->image_alt_text ?? '',
                        ];
                    });
            });

            return response()->json([
                'success' => true,
                'data'    => $popupAds
            ], 200);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Error retrieving popup ads: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }
}
