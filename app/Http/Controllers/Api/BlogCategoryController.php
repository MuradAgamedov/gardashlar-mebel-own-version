<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use App\Traits\LocaleFromHeader;
use OpenApi\Annotations as OA;

class BlogCategoryController extends Controller
{
    use LocaleFromHeader;

   
    public function index(Request $request): JsonResponse
    {
        try {
            $languageId = $this->getLanguageIdFromHeader($request);
            $page = $request->get('page', 1);
            $perPage = min($request->get('per_page', 10000), 50); // Max 50 per page
            
            $cacheKey = "api:blog-categories:lang:{$languageId}:page:{$page}:per_page:{$perPage}";

            $blogCategories = Cache::remember($cacheKey, 3600, function () use ($languageId, $page, $perPage) {
                return BlogCategory::active()
                    ->with(['translates' => fn($q) => $q->where('language_id', $languageId)])
                    ->withCount(['blogPosts' => function($q) {
                        $q->published();
                    }])
                    ->ordered()
                    ->paginate($perPage);
            });

            $data = $blogCategories->getCollection()->map(function ($blogCategory) {
                $translate = $blogCategory->translates->first();
                
                return [
                    'id' => $blogCategory->id,
                    'title' => $translate->title ?? null,
                    'slug' => $translate->slug ?? null,
                    'seo_title' => $translate->seo_title ?? null,
                    'seo_keywords' => $translate->seo_keywords ?? null,
                    'seo_description' => $translate->seo_description ?? null,
                    'posts_count' => $blogCategory->blog_posts_count,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $data,
                'pagination' => [
                    'current_page' => $blogCategories->currentPage(),
                    'last_page' => $blogCategories->lastPage(),
                    'per_page' => $blogCategories->perPage(),
                    'total' => $blogCategories->total(),
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving blog categories: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/blog-categories/{slug}",
     *     summary="Get blog category SEO data by slug",
     *     tags={"Blog Categories"},
     *     @OA\Parameter(
     *         name="slug",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string"),
     *         description="Blog category slug"
     *     ),
     *     @OA\Parameter(
     *         name="Accept-Language",
     *         in="header",
     *         required=false,
     *         @OA\Schema(type="string", example="az"),
     *         description="Language code (az, en, ru)"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Blog category SEO data retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="title", type="string", example="Technology"),
     *                 @OA\Property(property="slug", type="string", example="technology"),
     *                 @OA\Property(property="seo_title", type="string", example="Technology Blog - Latest Tech News"),
     *                 @OA\Property(property="seo_keywords", type="string", example="technology, tech news, innovation"),
     *                 @OA\Property(property="seo_description", type="string", example="Stay updated with the latest technology news and innovations"),
     *                 @OA\Property(property="posts_count", type="integer", example=15)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Blog category not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Blog category not found"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Error retrieving blog category"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     )
     * )
     */
    public function show(Request $request, string $slug): JsonResponse
    {
        try {
            $languageId = $this->getLanguageIdFromHeader($request);
            
            $cacheKey = "api:blog-category:slug:{$slug}:lang:{$languageId}";

            $blogCategory = Cache::remember($cacheKey, 3600, function () use ($slug, $languageId) {
                return BlogCategory::active()
                    ->whereHas('translates', function($q) use ($slug, $languageId) {
                        $q->where('slug', $slug)->where('language_id', $languageId);
                    })
                    ->with(['translates' => fn($q) => $q->where('language_id', $languageId)])
                    ->withCount(['blogPosts' => function($q) {
                        $q->published();
                    }])
                    ->first();
            });

            if (!$blogCategory) {
                return response()->json([
                    'success' => false,
                    'message' => 'Blog category not found',
                    'data' => (object)[]
                ], 404);
            }

            $translate = $blogCategory->translates->first();
            
            $data = [
                'id' => $blogCategory->id,
                'title' => $translate->title ?? null,
                'slug' => $translate->slug ?? null,
                'seo_title' => $translate->seo_title ?? null,
                'seo_keywords' => $translate->seo_keywords ?? null,
                'seo_description' => $translate->seo_description ?? null,
                'posts_count' => $blogCategory->blog_posts_count,
            ];

            return response()->json([
                'success' => true,
                'data' => $data
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving blog category: ' . $e->getMessage(),
                'data' => (object)[]
            ], 500);
        }
    }
}