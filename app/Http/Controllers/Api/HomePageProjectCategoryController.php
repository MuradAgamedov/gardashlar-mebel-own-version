<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Interfaces\Services\Front\HomePageProjectCategoryServiceInterface;
use App\Http\Resources\Api\HomePageProjectCategoryResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use App\Traits\LocaleFromHeader;

class HomePageProjectCategoryController extends Controller
{
    use LocaleFromHeader;

    protected HomePageProjectCategoryServiceInterface $homePageProjectCategoryService;

    public function __construct(HomePageProjectCategoryServiceInterface $homePageProjectCategoryService)
    {
        $this->homePageProjectCategoryService = $homePageProjectCategoryService;
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $languageId = $this->getLanguageIdFromHeader($request);
            $cacheKey = "api:home-page-project-categories:lang:{$languageId}";

            $items = Cache::rememberForever($cacheKey, function () use ($languageId) {
                return $this->homePageProjectCategoryService->getActiveOrderedWithTranslations($languageId, [
                    'projectCategory.translates' => fn($q) => $q->where('language_id', $languageId)
                ]);
            });

            $resources = $items->map(function ($item) use ($languageId) {
                $resource = new HomePageProjectCategoryResource($item);
                $resource->languageId = $languageId;
                return $resource;
            });

            return response()->json(['success' => true, 'data' => $resources], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ana səhifə layihə kateqoriyaları əldə edilərkən xəta baş verdi: ' . $e->getMessage(),
                'data' => [],
            ], 500);
        }
    }
}
