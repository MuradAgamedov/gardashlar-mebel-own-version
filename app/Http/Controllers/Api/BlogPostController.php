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

 
  public function index(Request $request): JsonResponse
{
    try {
        $languageId = $this->getLanguageIdFromHeader($request);
        $page = $request->get('page', 1);
        $perPage = min($request->get('per_page', 10), 50);
        $categorySlug = $request->get('category_slug');
        $sort = $request->get('sort', 'newest');

        $allowedSorts = ['newest', 'oldest'];
        if (!in_array($sort, $allowedSorts)) {
            $sort = 'newest';
        }

        $cacheKey = "api:blog-posts-v3:lang:{$languageId}:page:{$page}:per_page:{$perPage}:sort:{$sort}";
        if ($categorySlug) {
            $cacheKey .= ":category:{$categorySlug}";
        }

        $blogPosts = Cache::remember($cacheKey, 3600, function () use ($languageId, $page, $perPage, $categorySlug, $sort) {
            $query = BlogPost::published()
                ->with([
                    'translates' => fn($q) => $q->where('language_id', $languageId),
                    'category.translates.language'
                ]);

            if ($categorySlug) {
                $query->whereHas('category.translates', function ($q) use ($categorySlug) {
                    $q->where('slug', $categorySlug);
                });
            }

            switch ($sort) {
                case 'oldest':
                    $query->orderBy('published_at', 'asc');
                    break;
                default:
                    $query->orderBy('published_at', 'desc');
                    break;
            }

            return $query->paginate($perPage);
        });

        // Blog məlumatları (kateqoriya və tərcümə ilə)
        $data = $blogPosts->getCollection()->map(function ($blogPost) {
            $translate = $blogPost->translates->first();
            $categoryTranslate = $blogPost->category?->translates?->first();

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

        // Əgər category_slug seçilibsə, həmin kateqoriyanın bütün dillərdəki slug-larını alırıq
        $categorySlugTranslations = null;
        if ($categorySlug) {
            $category = \App\Models\BlogCategory::whereHas('translates', function ($q) use ($categorySlug) {
                $q->where('slug', $categorySlug);
            })
                ->with(['translates.language'])
                ->first();

            if ($category) {
                $categorySlugTranslations = $category->translates->mapWithKeys(function ($t) {
                    return [$t->language->lang_code => $t->slug];
                });
            }
        }

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
                    ['value' => 'newest', 'label' => __('newest')],
                    ['value' => 'oldest', 'label' => __('oldest')],
                ],
                'translates' => [
                    'slug' => $categorySlugTranslations ?? (object)[]
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


    
public function show(Request $request, string $slug): JsonResponse
{
    try {
        $languageId = $this->getLanguageIdFromHeader($request);
        $cacheKey = "api:blog-post:slug:{$slug}:lang:{$languageId}";

        $blogPost = Cache::remember($cacheKey, 3600, function () use ($slug, $languageId) {
            return BlogPost::published()
                ->with([
                    'translates.language',
                    'category.translates.language',
                    'images'
                ])
                ->whereHas('translates', function ($q) use ($slug) {
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

        $translate = $blogPost->translates->firstWhere('language_id', $languageId);
        $categoryTranslate = $blogPost->category?->translates?->firstWhere('language_id', $languageId);

        // 🔹 Bloqun bütün dillərdəki slug-ları
        $slugTranslations = $blogPost->translates->mapWithKeys(function ($t) {
            return [$t->language->lang_code => $t->slug];
        });

        // 🔹 Kateqoriyanın bütün dillərdəki slug-ları
        $categorySlugTranslations = $blogPost->category?->translates?->mapWithKeys(function ($t) {
            return [$t->language->lang_code => $t->slug];
        }) ?? collect();

        // 🔹 Oxşar bloglar
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
                    'card_image_url' => $similarBlog->card_image
                        ? asset('storage/' . $similarBlog->card_image)
                        : asset('images/default_image.webp'),
                    'card_description' => $similarTranslate->card_description ?? null,
                    'category_slug' => $similarCategoryTranslate->slug ?? null
                ];
            });

        // 🔹 Məlumatlar
        $data = [
            'id' => $blogPost->id,
            'title' => $translate->title ?? null,
            'slug' => $translate->slug ?? null,
            'description' => $translate->description ?? null,
            'card_image_url' => $blogPost->card_image
                ? asset('storage/' . $blogPost->card_image)
                : asset('images/default_image.webp'),
            'inner_banner_image_url' => $blogPost->inner_banner_image
                ? asset('storage/' . $blogPost->inner_banner_image)
                : asset('images/default_image.webp'),
            'published_at' => $blogPost->published_at,
            'seo_title' => $blogPost->seo_title ?? null,
            'seo_keywords' => $blogPost->seo_keywords ?? null,
            'seo_description' => $blogPost->seo_description ?? null,
            'category' => [
                'id' => $blogPost->category->id ?? null,
                'title' => $categoryTranslate->title ?? null,
                'slug' => $categoryTranslate->slug ?? null,
            ],
            'category_slug' => $categoryTranslate->slug ?? null,
            'images' => $blogPost->images->map(function ($image) {
                return [
                    'id' => $image->id,
                    'image_url' => $image->image_url
                        ? asset('storage/' . $image->image_url)
                        : asset('images/default_image.webp')
                ];
            }),
            'similar_blogs' => $similarBlogs,
            'translates' => [
                'slug' => $slugTranslations,
                'category_slug' => $categorySlugTranslations
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