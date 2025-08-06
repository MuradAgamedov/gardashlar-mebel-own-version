<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use App\Traits\LocaleFromHeader;
use OpenApi\Annotations as OA;

class BlogPostController extends Controller
{
    use LocaleFromHeader;

    /**
     * @OA\Get(
     *     path="/api/blog-posts",
     *     operationId="getBlogPosts",
     *     tags={"BlogPosts"},
     *     summary="Get all published blog posts with pagination",
     *     description="Returns a paginated list of published blog posts with card image, description, title and slug, ordered by published_at desc",
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
     *     @OA\Parameter(
     *         name="category_id",
     *         in="query",
     *         description="Filter by blog category ID",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *             example=1
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
     *                     @OA\Property(property="title", type="string", example="Blog Post Title"),
     *                     @OA\Property(property="slug", type="string", example="blog-post-slug"),
     *                     @OA\Property(property="card_image_url", type="string", example="https://example.com/image.jpg"),
     *                     @OA\Property(property="card_description", type="string", example="Short description for card")
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
     *             @OA\Property(property="message", type="string", example="Error retrieving blog posts")
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
            $categoryId = $request->get('category_id');
            
            $cacheKey = "api:blog-posts:lang:{$languageId}:page:{$page}:per_page:{$perPage}";
            if ($categoryId) {
                $cacheKey .= ":category:{$categoryId}";
            }

            $blogPosts = Cache::remember($cacheKey, 3600, function () use ($languageId, $page, $perPage, $categoryId) {
                $query = BlogPost::published()
                    ->with([
                        'translates' => fn($q) => $q->where('language_id', $languageId),
                        'images'
                    ])
                    ->orderBy('published_at', 'desc');

                // Filter by category if provided
                if ($categoryId) {
                    $query->where('blog_category_id', $categoryId);
                }

                return $query->paginate($perPage);
            });

            $data = $blogPosts->getCollection()->map(function ($blogPost) {
                $translate = $blogPost->translates->first();
                
                return [
                    'id' => $blogPost->id,
                    'title' => $translate->title ?? null,
                    'slug' => $translate->slug ?? null,
                    'card_image' => $blogPost->card_image,
                    'card_image_url' => $blogPost->card_image_url,
                    'card_description' => $translate->card_description ?? null,
                    'images' => $blogPost->images->map(function ($image) {
                        return [
                            'id' => $image->id,
                            'image' => $image->image,
                            'image_url' => $image->image ? asset('storage/' . $image->image) : null,
                            'alt_text' => $image->alt_text,
                            'order' => $image->order
                        ];
                    })
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $data,
                'pagination' => [
                    'current_page' => $blogPosts->currentPage(),
                    'last_page' => $blogPosts->lastPage(),
                    'per_page' => $blogPosts->perPage(),
                    'total' => $blogPosts->total(),
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving blog posts: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/blog-posts/{slug}",
     *     operationId="getBlogPostBySlug",
     *     tags={"BlogPosts"},
     *     summary="Get a specific blog post by slug",
     *     description="Returns a specific blog post with full details by slug",
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
     *         name="slug",
     *         in="path",
     *         description="Blog post slug",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             example="blog-post-slug"
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
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="title", type="string", example="Blog Post Title"),
     *                 @OA\Property(property="slug", type="string", example="blog-post-slug"),
     *                 @OA\Property(property="description", type="string", example="Full blog post content"),
     *                 @OA\Property(property="card_image_url", type="string", example="https://example.com/image.jpg"),
     *                 @OA\Property(property="inner_banner_image_url", type="string", example="https://example.com/banner.jpg"),
     *                 @OA\Property(property="published_at", type="string", example="2024-01-01T00:00:00.000000Z"),
     *                 @OA\Property(
     *                     property="category",
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="title", type="string", example="Category Title")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Blog post not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Blog post not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Error retrieving blog post")
     *         )
     *     )
     * )
     */
    public function show(Request $request, string $slug): JsonResponse
    {
        try {
            $languageId = $this->getLanguageIdFromHeader($request);
            $cacheKey = "api:blog-post:slug:{$slug}:lang:{$languageId}";

            $blogPost = Cache::remember($cacheKey, 3600, function () use ($slug, $languageId) {
                return BlogPost::published()
                    ->with([
                        'translates' => fn($q) => $q->where('language_id', $languageId),
                        'category.translates' => fn($q) => $q->where('language_id', $languageId)
                    ])
                    ->whereHas('translates', function($q) use ($slug) {
                        $q->where('slug', $slug);
                    })
                    ->first();
            });

            if (!$blogPost) {
                return response()->json([
                    'success' => false,
                    'message' => 'Blog post not found',
                    'data' => null
                ], 404);
            }

            $translate = $blogPost->translates->first();
            $categoryTranslate = $blogPost->category->translates->first();

            $data = [
                'id' => $blogPost->id,
                'title' => $translate->title ?? null,
                'slug' => $translate->slug ?? null,
                'description' => $translate->description ?? null,
                'card_image_url' => $blogPost->card_image ? asset('storage/' . $blogPost->card_image) : null,
                'inner_banner_image_url' => $blogPost->inner_banner_image ? asset('storage/' . $blogPost->inner_banner_image) : null,
                'published_at' => $blogPost->published_at,
                'category' => [
                    'id' => $blogPost->category->id,
                    'title' => $categoryTranslate->title ?? null,
                ]
            ];

            return response()->json([
                'success' => true,
                'data' => $data
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving blog post: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }
} 