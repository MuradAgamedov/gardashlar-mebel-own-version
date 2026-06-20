<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\NewsCategoryResource;
use App\Http\Resources\Api\NewsCategoryDetailResource;
use App\Models\NewsCategory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use App\Traits\LocaleFromHeader;

class NewsCategoryController extends Controller
{
    use LocaleFromHeader;

    public function index(Request $request): JsonResponse
    {
        try {
            $languageId = $this->getLanguageIdFromHeader($request);
            $page = $request->get('page', 1);
            $perPage = min($request->get('per_page', 10000), 50);

            $cacheKey = "api:news-categories:lang:{$languageId}:page:{$page}:per_page:{$perPage}";

            $newsCategories = Cache::remember($cacheKey, 3600, function () use ($languageId, $page, $perPage) {
                return NewsCategory::active()
                    ->with(['translates' => fn($q) => $q->where('language_id', $languageId)])
                    ->withCount(['newsPosts' => fn($q) => $q->published()])
                    ->ordered()
                    ->paginate($perPage);
            });

            return response()->json([
                'success' => true,
                'data' => NewsCategoryResource::collection($newsCategories->getCollection()),
                'pagination' => [
                    'current_page' => $newsCategories->currentPage(),
                    'last_page' => $newsCategories->lastPage(),
                    'per_page' => $newsCategories->perPage(),
                    'total' => $newsCategories->total(),
                ],
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving news categories: ' . $e->getMessage(),
                'data' => [],
            ], 500);
        }
    }

    public function show(Request $request, string $slug): JsonResponse
    {
        try {
            $languageId = $this->getLanguageIdFromHeader($request);
            $cacheKey = "api:news-category:slug:{$slug}:lang:{$languageId}";

            $newsCategory = Cache::remember($cacheKey, 3600, function () use ($slug, $languageId) {
                return NewsCategory::active()
                    ->whereHas('translates', fn($q) => $q->where('slug', $slug)->where('language_id', $languageId))
                    ->with(['translates.language'])
                    ->withCount(['newsPosts' => fn($q) => $q->published()])
                    ->first();
            });

            if (!$newsCategory) {
                return response()->json([
                    'success' => false,
                    'message' => 'News category not found',
                    'data' => (object)[],
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => new NewsCategoryDetailResource($newsCategory),
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving news category: ' . $e->getMessage(),
                'data' => (object)[],
            ], 500);
        }
    }
}
