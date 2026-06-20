<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Interfaces\Services\Front\BlogPostServiceInterface;
use App\Http\Resources\Api\BlogPostListResource;
use App\Http\Resources\Api\BlogPostDetailResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use App\Traits\LocaleFromHeader;

class BlogPostController extends Controller
{
    use LocaleFromHeader;

    protected BlogPostServiceInterface $blogPostService;

    public function __construct(BlogPostServiceInterface $blogPostService)
    {
        $this->blogPostService = $blogPostService;
    }

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

            $cacheKey = "api:blog-posts-v3:lang:{$languageId}:page:{$page}:per_page:{$perPage}:sort:{$sort}";
            if ($categorySlug) {
                $cacheKey .= ":category:{$categorySlug}";
            }

            $blogPosts = Cache::remember($cacheKey, 3600, function () use ($languageId, $perPage, $categorySlug, $sort) {
                return $this->blogPostService->getPublishedPaginated($languageId, $perPage, $categorySlug, $sort);
            });

            $categorySlugTranslations = null;
            if ($categorySlug) {
                $category = \App\Models\BlogCategory::whereHas('translates', fn($q) => $q->where('slug', $categorySlug))
                    ->with(['translates.language'])
                    ->first();

                if ($category) {
                    $categorySlugTranslations = $category->translates->mapWithKeys(fn($t) => [$t->language->lang_code => $t->slug]);
                }
            }

            return $this->success([
                'blogs' => BlogPostListResource::collection($blogPosts->getCollection()),
                'pagination' => [
                    'current_page' => $blogPosts->currentPage(),
                    'last_page' => $blogPosts->lastPage(),
                    'per_page' => $blogPosts->perPage(),
                    'total' => $blogPosts->total(),
                ],
                'sort_options' => [
                    ['value' => 'newest', 'label' => __('newest')],
                    ['value' => 'oldest', 'label' => __('oldest')],
                ],
                'translates' => [
                    'slug' => $categorySlugTranslations ?? (object)[],
                ],
            ]);

        } catch (\Exception $e) {
            return $this->serverError('Error retrieving blog posts: ' . $e->getMessage());
        }
    }

    public function show(Request $request, string $slug): JsonResponse
    {
        try {
            $languageId = $this->getLanguageIdFromHeader($request);
            $cacheKey = "api:blog-post:slug:{$slug}:lang:{$languageId}";

            $blogPost = Cache::remember($cacheKey, 3600, function () use ($slug, $languageId) {
                return $this->blogPostService->getPublishedBySlug($slug, $languageId);
            });

            if (!$blogPost) {
                return $this->notFound('Blog post not found');
            }

            $resource = new BlogPostDetailResource($blogPost);
            $resource->languageId = $languageId;

            return $this->success($resource);

        } catch (\Exception $e) {
            return $this->serverError('Error retrieving blog post: ' . $e->getMessage());
        }
    }
}
