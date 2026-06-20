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
                return $this->notFound('No KIVs found');
            }

            return $this->success(KivCategoryResource::collection($categories), 'KIVs retrieved successfully');

        } catch (\Exception $e) {
            return $this->serverError('Error retrieving KIVs: ' . $e->getMessage());
        }
    }
}

