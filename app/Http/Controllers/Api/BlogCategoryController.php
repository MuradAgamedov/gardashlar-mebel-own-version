<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Interfaces\Services\Front\BlogCategoryServiceInterface;
use App\Http\Resources\Api\BlogCategoryResource;
use App\Http\Resources\Api\BlogCategoryDetailResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use App\Traits\LocaleFromHeader;

class BlogCategoryController extends Controller
{
    use LocaleFromHeader;

    protected BlogCategoryServiceInterface $blogCategoryService;

    public function __construct(BlogCategoryServiceInterface $blogCategoryService)
    {
        $this->blogCategoryService = $blogCategoryService;
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $languageId = $this->getLanguageIdFromHeader($request);
            $page = $request->get('page', 1);
            $perPage = min($request->get('per_page', 10000), 50);

            $cacheKey = "api:blog-categories:lang:{$languageId}:page:{$page}:per_page:{$perPage}";

            $blogCategories = Cache::remember($cacheKey, 3600, function () use ($languageId, $perPage) {
                return $this->blogCategoryService->getActivePaginatedWithCount($languageId, $perPage);
            });

            return $this->success([
                'data' => BlogCategoryResource::collection($blogCategories->getCollection()),
                'pagination' => [
                    'current_page' => $blogCategories->currentPage(),
                    'last_page' => $blogCategories->lastPage(),
                    'per_page' => $blogCategories->perPage(),
                    'total' => $blogCategories->total(),
                ],
            ]);

        } catch (\Exception $e) {
            return $this->serverError('Error retrieving blog categories: ' . $e->getMessage());
        }
    }

    public function show(Request $request, string $slug): JsonResponse
    {
        try {
            $languageId = $this->getLanguageIdFromHeader($request);
            $cacheKey = "api:blog-category:slug:{$slug}:lang:{$languageId}";

            $blogCategory = Cache::remember($cacheKey, 3600, function () use ($slug, $languageId) {
                return $this->blogCategoryService->getActiveBySlug($slug, $languageId);
            });

            if (!$blogCategory) {
                return $this->notFound('Blog category not found');
            }

            return $this->success(new BlogCategoryDetailResource($blogCategory));

        } catch (\Exception $e) {
            return $this->serverError('Error retrieving blog category: ' . $e->getMessage());
        }
    }
}
