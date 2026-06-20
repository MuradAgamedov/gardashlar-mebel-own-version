<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Interfaces\Services\Front\SuggestAPlaceServiceInterface;
use App\Http\Resources\Api\SuggestAPlaceResource;
use App\Traits\LocaleFromHeader;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use OpenApi\Annotations as OA;


class SuggestAPlaceController extends Controller
{
    use LocaleFromHeader;

    protected SuggestAPlaceServiceInterface $suggestAPlaceService;

    public function __construct(SuggestAPlaceServiceInterface $suggestAPlaceService)
    {
        $this->suggestAPlaceService = $suggestAPlaceService;
    }
   
    public function index(Request $request): JsonResponse
    {
        try {
            $languageId = $this->getLanguageIdFromHeader($request);
            $cacheKey = "api_suggest_a_place_{$languageId}";
            
            $suggestAPlace = Cache::rememberForever($cacheKey, function () use ($languageId) {
                return $this->suggestAPlaceService->first(['translates' => fn($q) => $q->where('language_id', $languageId)]);
            });

            if (!$suggestAPlace) {
                return $this->notFound('Suggest a place data not found');
            }

            return $this->success(new SuggestAPlaceResource($suggestAPlace), 'Suggest a place data retrieved successfully');
            
        } catch (\Exception $e) {
            return $this->serverError('Error retrieving suggest a place data: ' . $e->getMessage());
        }
    }
}
