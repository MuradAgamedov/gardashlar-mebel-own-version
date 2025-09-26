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
     *         name="category_slug",
     *         in="query",
     *         description="Filter by blog category slug",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *             example="technology"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="sort",
     *         in="query",
     *         description="Sort blog posts by: newest, oldest",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *             example="newest"
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
     *                 @OA\Property(
     *                     property="blogs",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="title", type="string", example="Blog Post Title"),
     *                         @OA\Property(property="slug", type="string", example="blog-post-slug"),
     *                         @OA\Property(property="card_image_url", type="string", example="https://example.com/image.jpg"),
     *                         @OA\Property(property="card_description", type="string", example="Short description for card"),
     *                         @OA\Property(property="category_slug", type="string", example="category-slug")
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="pagination",
     *                     type="object",
     *                     @OA\Property(property="current_page", type="integer", example=1),
     *                     @OA\Property(property="last_page", type="integer", example=5),
     *                     @OA\Property(property="per_page", type="integer", example=10),
     *                     @OA\Property(property="total", type="integer", example=50)
     *                 ),
     *                 @OA\Property(
     *                     property="sort_options",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="value", type="string", example="newest"),
     *                         @OA\Property(property="label", type="string", example="newest")
     *                     )
     *                 )
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
            $categorySlug = $request->get('category_slug');
            $sort = $request->get('sort', 'newest');
            
            $cacheKey = "api:blog-posts:lang:{$languageId}:page:{$page}:per_page:{$perPage}:sort:{$sort}";
            if ($categorySlug) {
                $cacheKey .= ":category:{$categorySlug}";
            }

            $blogPosts = Cache::remember($cacheKey, 3600, function () use ($languageId, $page, $perPage, $categorySlug, $sort) {
                $query = BlogPost::published()
                    ->with([
                        'translates' => fn($q) => $q->where('language_id', $languageId),
                        'category.translates' => fn($q) => $q->where('language_id', $languageId)
                    ]);

                // Apply sorting
                switch ($sort) {
                    case 'oldest':
                        $query->orderBy('published_at', 'asc');
                        break;
                    case 'newest':
                    default:
                        $query->orderBy('published_at', 'desc');
                        break;
                }

                // Filter by category slug if provided
                if ($categorySlug) {
                    $query->whereHas('category.translates', function($q) use ($categorySlug) {
                        $q->where('slug', $categorySlug);
                    });
                }

                return $query->paginate($perPage);
            });

            $data = $blogPosts->getCollection()->map(function ($blogPost) {
                $translate = $blogPost->translates->first();
                $categoryTranslate = $blogPost->category->translates->first();
                
                return [
                    'id' => $blogPost->id,
                    'title' => $translate->title ?? null,
                    'slug' => $translate->slug ?? null,
                    'card_image' => $blogPost->card_image,
                    'card_image_url' => $blogPost->card_image_url,
                    'card_description' => $translate->card_description ?? null,
                    'category_slug' => $categoryTranslate->slug ?? null,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'blogs' => $data,
                    'pagination' => [
                        'current_page' => $blogPosts->currentPage(),
                        'last_page' => $blogPosts->lastPage(),
                        'per_page' => $blogPosts->perPage(),
                        'total' => $blogPosts->total(),
                    ],
                    'sort_options' => [
                        ['value' => 'newest', 'label' => 'newest'],
                        ['value' => 'oldest', 'label' => 'oldest']
                    ]
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
     *                     @OA\Property(property="title", type="string", example="Category Title"),
     *                     @OA\Property(property="slug", type="string", example="category-slug")
     *                 ),
     *                 @OA\Property(property="category_slug", type="string", example="category-slug"),
     *                 @OA\Property(
     *                     property="images",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="image_url", type="string", example="https://example.com/storage/uploads/blogs/images/image1.jpg")
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="similar_blogs",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=2),
     *                         @OA\Property(property="title", type="string", example="Similar Blog Post"),
     *                         @OA\Property(property="slug", type="string", example="similar-blog-post"),
     *                         @OA\Property(property="card_image_url", type="string", example="https://example.com/similar-image.jpg"),
     *                         @OA\Property(property="card_description", type="string", example="Similar blog description"),
     *                         @OA\Property(property="category_slug", type="string", example="category-slug")
     *                     )
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
                        'category.translates' => fn($q) => $q->where('language_id', $languageId),
                        'images'
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

            // Get similar blogs (same category, excluding current blog)
            $similarBlogs = BlogPost::published()
                ->with([
                    'translates' => fn($q) => $q->where('language_id', $languageId),
                    'category.translates' => fn($q) => $q->where('language_id', $languageId)
                ])
                ->where('blog_category_id', $blogPost->blog_category_id)
                ->where('id', '!=', $blogPost->id)
                ->orderBy('published_at', 'desc')
                ->limit(4)
                ->get()
                ->map(function ($similarBlog) {
                    $similarTranslate = $similarBlog->translates->first();
                    $similarCategoryTranslate = $similarBlog->category->translates->first();
                    return [
                        'id' => $similarBlog->id,
                        'title' => $similarTranslate->title ?? null,
                        'slug' => $similarTranslate->slug ?? null,
                        'card_image_url' => $similarBlog->card_image ? asset('storage/' . $similarBlog->card_image) : asset('images/default_image.webp'),
                        'card_description' => $similarTranslate->card_description ?? null,
                        'category_slug' => $similarCategoryTranslate->slug ?? null
                    ];
                });

            $data = [
                'id' => $blogPost->id,
                'title' => $translate->title ?? null,
                'slug' => $translate->slug ?? null,
                'description' => $translate->description ?? null,
                'card_image_url' => $blogPost->card_image ? asset('storage/' . $blogPost->card_image) : asset('images/default_image.webp'),
                'inner_banner_image_url' => $blogPost->inner_banner_image ? asset('storage/' . $blogPost->inner_banner_image) : asset('images/default_image.webp'),
                'published_at' => $blogPost->published_at,
                'seo_title' => $blogPost->seo_title ?? null,
                'seo_keywords' => $blogPost->seo_keywords ?? null,
                'seo_description' => $blogPost->seo_description ?? null,
                'category' => [
                    'id' => $blogPost->category->id,
                    'title' => $categoryTranslate->title ?? null,
                    'slug' => $categoryTranslate->slug ?? null,
                ],
                'category_slug' => $categoryTranslate->slug ?? null,
                'images' => $blogPost->images->map(function ($image) {
                    return [
                        'id' => $image->id,
                        'image_url' => $image->image_url ? asset('storage/' . $image->image_url) : asset('images/default_image.webp')
                    ];
                }),
                'similar_blogs' => $similarBlogs
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