<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NewsCategory;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use App\Traits\LocaleFromHeader;
use OpenApi\Annotations as OA;

class NewsCategoryController extends Controller
{
    use LocaleFromHeader;

   
    public function index(Request $request): JsonResponse
    {
        try {
            $languageId = $this->getLanguageIdFromHeader($request);
            $page = $request->get('page', 1);
            $perPage = min($request->get('per_page', 10000), 50); // Max 50 per page
            
            $cacheKey = "api:news-categories:lang:{$languageId}:page:{$page}:per_page:{$perPage}";

            $newsCategories = Cache::remember($cacheKey, 3600, function () use ($languageId, $page, $perPage) {
                return NewsCategory::active()
                    ->with(['translates' => fn($q) => $q->where('language_id', $languageId)])
                    ->withCount(['newsPosts' => function($q) {
                        $q->published();
                    }])
                    ->ordered()
                    ->paginate($perPage);
            });

            $data = $newsCategories->getCollection()->map(function ($newsCategory) {
                $translate = $newsCategory->translates->first();
                
                return [
                    'id' => $newsCategory->id,
                    'title' => $translate->title ?? null,
                    'slug' => $translate->slug ?? null,
                    'posts_count' => $newsCategory->news_posts_count,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $data,
                'pagination' => [
                    'current_page' => $newsCategories->currentPage(),
                    'last_page' => $newsCategories->lastPage(),
                    'per_page' => $newsCategories->perPage(),
                    'total' => $newsCategories->total(),
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving news categories: ' . $e->getMessage(),
                'data' => []
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
                    ->whereHas('translates', function($q) use ($slug, $languageId) {
                        $q->where('slug', $slug)->where('language_id', $languageId);
                    })
                    ->with(['translates' => fn($q) => $q->where('language_id', $languageId)])
                    ->withCount(['newsPosts' => function($q) {
                        $q->published();
                    }])
                    ->first();
            });

            if (!$newsCategory) {
                return response()->json([
                    'success' => false,
                    'message' => 'News category not found',
                    'data' => (object)[]
                ], 404);
            }

            $translate = $newsCategory->translates->first();
            
            $languages = Language::all();
            $slugTranslations = [];
            
            foreach ($languages as $lang) {
                $langCode = $lang->lang_code;
                $tr = $newsCategory->translates()->where('language_id', $lang->id)->first();
                $slugTranslations[$langCode] = $tr ? $tr->slug : null;
            }

            $data = [
                'id' => $newsCategory->id,
                'title' => $translate->title ?? null,
                'slug' => $translate->slug ?? null,
                'seo_title' => $translate->seo_title ?? null,
                'seo_keywords' => $translate->seo_keywords ?? null,
                'seo_description' => $translate->seo_description ?? null,
                'posts_count' => $newsCategory->news_posts_count,
                'translates' => [
                    'slug' => $slugTranslations
                ]
            ];

            return response()->json([
                'success' => true,
                'data' => $data
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving news category: ' . $e->getMessage(),
                'data' => (object)[]
            ], 500);
        }
    }
}
