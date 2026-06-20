<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Interfaces\Services\Front\HomePageCategoryServiceInterface;
use App\Http\Resources\Api\HomePageCategoryResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use App\Traits\LocaleFromHeader;

class HomePageCategoryController extends Controller
{
    use LocaleFromHeader;

    protected HomePageCategoryServiceInterface $homePageCategoryService;

    public function __construct(HomePageCategoryServiceInterface $homePageCategoryService)
    {
        $this->homePageCategoryService = $homePageCategoryService;
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $languageId = $this->getLanguageIdFromHeader($request);
            $cacheKey = "api:homepage-categories:lang:{$languageId}";

            $homePageCategories = Cache::rememberForever($cacheKey, function () use ($languageId) {
                return $this->homePageCategoryService->getActiveOrderedWithTranslations($languageId, [
                    'productCategory.translates' => fn($q) => $q->where('language_id', $languageId)
                ]);
            });

            $resources = $homePageCategories->map(function ($item) use ($languageId) {
                $resource = new HomePageCategoryResource($item);
                $resource->languageId = $languageId;
                return $resource;
            });

            return response()->json([
                'success' => true,
                'data' => $resources,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving homepage categories: ' . $e->getMessage(),
                'data' => [],
            ], 500);
        }
    }
}
