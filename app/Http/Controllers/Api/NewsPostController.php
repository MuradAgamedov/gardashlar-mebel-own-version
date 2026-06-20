<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\NewsPostListResource;
use App\Http\Resources\Api\NewsPostDetailResource;
use App\Models\NewsPost;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use App\Traits\LocaleFromHeader;

class NewsPostController extends Controller
{
    use LocaleFromHeader;

    public function index(Request $request): JsonResponse
    {
        try {
            $languageId = $this->getLanguageIdFromHeader($request);
            $lang = $request->header('locale');
            app()->setLocale($lang);
            $page = $request->get('page', 1);
            $perPage = min($request->get('per_page', 10), 50);
            $categorySlug = $request->get('category_slug');
            $sort = $request->get('sort', 'newest');

            if (!in_array($sort, ['newest', 'oldest'])) {
                $sort = 'newest';
            }

            $cacheKey = "api:news-posts-v3:lang:{$languageId}:page:{$page}:per_page:{$perPage}:sort:{$sort}";
            if ($categorySlug) {
                $cacheKey .= ":category:{$categorySlug}";
            }

            $newsPosts = Cache::remember($cacheKey, 3600, function () use ($languageId, $page, $perPage, $categorySlug, $sort) {
                $query = NewsPost::published()
                    ->with([
                        'translates.language',
                        'category.translates' => fn($q) => $q->where('language_id', $languageId),
                    ]);

                if ($categorySlug) {
                    $query->whereHas('category.translates', fn($q) => $q->where('slug', $categorySlug));
                }

                $query->orderBy('published_at', $sort === 'oldest' ? 'asc' : 'desc');

                return $query->paginate($perPage);
            });

            // Filter translates to current language for each post (translates.language is fully loaded)
            $collection = $newsPosts->getCollection()->each(function ($post) use ($languageId) {
                $post->setRelation('translates', $post->translates->where('language_id', $languageId));
            });

            $categorySlugTranslations = null;
            if ($categorySlug) {
                $category = \App\Models\NewsCategory::whereHas('translates', fn($q) => $q->where('slug', $categorySlug))
                    ->with(['translates.language'])
                    ->first();

                if ($category) {
                    $categorySlugTranslations = $category->translates->mapWithKeys(fn($t) => [$t->language->lang_code => $t->slug]);
                }
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'news' => NewsPostListResource::collection($collection),
                    'pagination' => [
                        'current_page' => $newsPosts->currentPage(),
                        'last_page' => $newsPosts->lastPage(),
                        'per_page' => $newsPosts->perPage(),
                        'total' => $newsPosts->total(),
                    ],
                    'sort_options' => [
                        ['value' => 'newest', 'label' => __('newest')],
                        ['value' => 'oldest', 'label' => __('oldest')],
                    ],
                    'translates' => [
                        'slug' => $categorySlugTranslations ?? (object)[],
                    ],
                ],
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving news posts: ' . $e->getMessage(),
                'data' => [],
            ], 500);
        }
    }

    public function show(Request $request, string $slug): JsonResponse
    {
        try {
            $languageId = $this->getLanguageIdFromHeader($request);
            $cacheKey = "api:news-post:slug:{$slug}:lang:{$languageId}";

            $newsPost = Cache::remember($cacheKey, 3600, function () use ($slug, $languageId) {
                return NewsPost::published()
                    ->with([
                        'translates.language',
                        'category.translates.language',
                        'images',
                    ])
                    ->whereHas('translates', fn($q) => $q->where('slug', $slug))
                    ->first();
            });

            if (!$newsPost) {
                return response()->json([
                    'success' => false,
                    'message' => 'News post not found',
                    'data' => null,
                ], 404);
            }

            $resource = new NewsPostDetailResource($newsPost);
            $resource->languageId = $languageId;

            return response()->json([
                'success' => true,
                'data' => $resource,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving news post: ' . $e->getMessage(),
                'data' => null,
            ], 500);
        }
    }
}
