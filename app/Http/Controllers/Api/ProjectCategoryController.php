<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\ProjectCategoryResource;
use App\Models\ProjectCategory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use App\Traits\LocaleFromHeader;

class ProjectCategoryController extends Controller
{
    use LocaleFromHeader;

    public function index(Request $request): JsonResponse
    {
        try {
            $languageId = $this->getLanguageIdFromHeader($request);
            $cacheKey = "api:project-categories:all:lang:{$languageId}";

            $projectCategories = Cache::rememberForever($cacheKey, function () use ($languageId) {
                return ProjectCategory::with(['translates.language'])
                    ->active()
                    ->ordered()
                    ->get();
            });

            $resources = $projectCategories
                ->filter(fn($c) => !empty($c->translates->where('language_id', $languageId)->first()?->title))
                ->values()
                ->map(function ($category) use ($languageId) {
                    $resource = new ProjectCategoryResource($category);
                    $resource->languageId = $languageId;
                    return $resource;
                });

            return response()->json(['success' => true, 'data' => $resources], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Layihə kateqoriyaları əldə edilərkən xəta baş verdi: ' . $e->getMessage(),
                'data' => [],
            ], 500);
        }
    }
}
