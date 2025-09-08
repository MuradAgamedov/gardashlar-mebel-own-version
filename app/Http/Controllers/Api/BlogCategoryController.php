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

    /**
     * @OA\Get(
     *     path="/api/blog-categories",
     *     operationId="getBlogCategories",
     *     tags={"BlogCategories"},
     *     summary="Get all active blog categories with pagination",
     *     description="Returns a paginated list of active blog categories with title and slug, ordered by 'order' field",
     *     @OA\Parameter(
     *         name="locale",
     *         in="header",
     *         description="Language_id from languages api",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *             example=1
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number for pagination",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *             example=1
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of items per page (max 50)",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *             example=10
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="title", type="string", example="Category Title"),
     *                     @OA\Property(property="slug", type="string", example="category-slug"),
     *                     @OA\Property(property="posts_count", type="integer", example=5)
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="pagination",
     *                 type="object",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="last_page", type="integer", example=5),
     *                 @OA\Property(property="per_page", type="integer", example=10),
     *                 @OA\Property(property="total", type="integer", example=50)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Error retrieving blog categories")
     *         )
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $languageId = $this->getLanguageIdFromHeader($request);
            $page = $request->get('page', 1);
            $perPage = min($request->get('per_page', 10), 50); // Max 50 per page
            
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
} 