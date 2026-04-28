<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NewsPost;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use App\Traits\LocaleFromHeader;
use OpenApi\Annotations as OA;

class NewsPostController extends Controller
{
    use LocaleFromHeader;

 
  public function index(Request $request): JsonResponse
{
    try {
        $languageId = $this->getLanguageIdFromHeader($request);
        $lang = $request->header('locale');
        
        // Debug: Check what language we're actually getting
        $mainLanguage = \App\Models\Language::where('is_main_language', true)->first();
        $requestedLanguage = \App\Models\Language::find($languageId);
        
        app()->setLocale($lang);
        $page = $request->get('page', 1);
        $perPage = min($request->get('per_page', 10), 50);
        $categorySlug = $request->get('category_slug');
        $sort = $request->get('sort', 'newest');

        
        // Temporary debug response
        return response()->json([
            'debug' => [
                'header_locale' => $lang,
                'detected_language_id' => $languageId,
                'main_language' => $mainLanguage ? [
                    'id' => $mainLanguage->id,
                    'lang_code' => $mainLanguage->lang_code,
                    'name' => $mainLanguage->name
                ] : null,
                'requested_language' => $requestedLanguage ? [
                    'id' => $requestedLanguage->id,
                    'lang_code' => $requestedLanguage->lang_code,
                    'name' => $requestedLanguage->name
                ] : null
            ]
        ]);

        $allowedSorts = ['newest', 'oldest'];
        if (!in_array($sort, $allowedSorts)) {
            $sort = 'newest';
        }

        // When filtering by category_slug, don't include language in cache key to show all languages
        if ($categorySlug) {
            $cacheKey = "api:news-posts-v3:category:{$categorySlug}:page:{$page}:per_page:{$perPage}:sort:{$sort}";
        } else {
            $cacheKey = "api:news-posts-v3:lang:{$languageId}:page:{$page}:per_page:{$perPage}:sort:{$sort}";
        }

        $newsPosts = Cache::remember($cacheKey, 3600, function () use ($languageId, $page, $perPage, $categorySlug, $sort) {
            // When filtering by category_slug, load all translates to show content in any language
            $translatesRelation = $categorySlug 
                ? 'translates.language' 
                : fn($q) => $q->where('language_id', $languageId);
                
            $query = NewsPost::published()
                ->with([
                    'translates' => $translatesRelation,
                    'category.translates.language'
                ]);

            if ($categorySlug) {
                // Find the category by slug first, then filter by category ID
                $category = \App\Models\NewsCategory::whereHas('translates', function ($q) use ($categorySlug) {
                    $q->where('slug', $categorySlug);
                })->first();
                
                                
                if ($category) {
                    $query->where('news_category_id', $category->id);
                } else {
                    // If category not found, return empty result
                    return new \Illuminate\Pagination\LengthAwarePaginator([], 0, $perPage);
                }
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

        // News məlumatları (kateqoriya və tərcümə ilə)
        $data = $newsPosts->getCollection()->map(function ($newsPost) use ($languageId, $categorySlug) {
            // When filtering by category_slug, try to get translate in requested language first, then any language
            if ($categorySlug) {
                $translate = $newsPost->translates->where('language_id', $languageId)->first() 
                    ?? $newsPost->translates->first();
                $categoryTranslate = $newsPost->category?->translates->where('language_id', $languageId)->first()
                    ?? $newsPost->category?->translates->first();
            } else {
                $translate = $newsPost->translates->first();
                $categoryTranslate = $newsPost->category?->translates?->first();
            }

            // When filtering by category_slug, return the filtered slug instead of the translated one
            $responseCategorySlug = $categorySlug ?? ($categoryTranslate->slug ?? null);

            return [
                'id' => $newsPost->id,
                'title' => $translate->title ?? null,
                'slug' => $translate->slug ?? null,
                'card_image' => $newsPost->card_image,
                'card_image_url' => $newsPost->card_image_url,
                'card_description' => $translate->card_description ?? null,
                'category_slug' => $responseCategorySlug,
                'language_code' => $translate->language->lang_code ?? null,
            ];
        });

        // Əgər category_slug seçilibsə, həmin kateqoriyanın bütün dillərdəki slug-larını alırıq
        $categorySlugTranslations = null;
        if ($categorySlug) {
            $category = \App\Models\NewsCategory::whereHas('translates', function ($q) use ($categorySlug) {
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
                'news' => $data,
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
                    'slug' => $categorySlugTranslations ?? (object)[]
                ]
            ]
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error retrieving news posts: ' . $e->getMessage(),
            'data' => []
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
                    'images'
                ])
                ->whereHas('translates', function ($q) use ($slug) {
                    $q->where('slug', $slug);
                })
                ->first();
        });

        if (!$newsPost) {
            return response()->json([
                'success' => false,
                'message' => 'News post not found',
                'data' => null
            ], 404);
        }

        $translate = $newsPost->translates->firstWhere('language_id', $languageId);
        $categoryTranslate = $newsPost->category?->translates?->firstWhere('language_id', $languageId);

        // 🔹 Xəbərin bütün dillərdəki slug-ları
        $slugTranslations = $newsPost->translates->mapWithKeys(function ($t) {
            return [$t->language->lang_code => $t->slug];
        });

        // 🔹 Kateqoriyanın bütün dillərdəki slug-ları
        $categorySlugTranslations = $newsPost->category?->translates?->mapWithKeys(function ($t) {
            return [$t->language->lang_code => $t->slug];
        }) ?? collect();

        // 🔹 Oxşar xəbərlər
        $similarNews = NewsPost::published()
            ->with([
                'translates' => fn($q) => $q->where('language_id', $languageId),
                'category.translates' => fn($q) => $q->where('language_id', $languageId)
            ])
            ->where('news_category_id', $newsPost->news_category_id)
            ->where('id', '!=', $newsPost->id)
            ->orderBy('published_at', 'desc')
            ->limit(4)
            ->get()
            ->map(function ($similarNews) {
                $similarTranslate = $similarNews->translates->first();
                $similarCategoryTranslate = $similarNews->category->translates->first();
                return [
                    'id' => $similarNews->id,
                    'title' => $similarTranslate->title ?? null,
                    'slug' => $similarTranslate->slug ?? null,
                    'card_image_url' => $similarNews->card_image
                        ? asset('storage/' . $similarNews->card_image)
                        : asset('images/default_image.webp'),
                    'card_description' => $similarTranslate->card_description ?? null,
                    'category_slug' => $similarCategoryTranslate->slug ?? null
                ];
            });

        // 🔹 Məlumatlar
        $data = [
            'id' => $newsPost->id,
            'title' => $translate->title ?? null,
            'slug' => $translate->slug ?? null,
            'description' => $translate->description ?? null,
            'card_image_url' => $newsPost->card_image
                ? asset('storage/' . $newsPost->card_image)
                : asset('images/default_image.webp'),
            'inner_banner_image_url' => $newsPost->inner_banner_image
                ? asset('storage/' . $newsPost->inner_banner_image)
                : asset('images/default_image.webp'),
            'published_at' => $newsPost->published_at,
            'seo_title' => $newsPost->seo_title ?? null,
            'seo_keywords' => $newsPost->seo_keywords ?? null,
            'seo_description' => $newsPost->seo_description ?? null,
            'category' => [
                'id' => $newsPost->category->id ?? null,
                'title' => $categoryTranslate->title ?? null,
                'slug' => $categoryTranslate->slug ?? null,
            ],
            'category_slug' => $categoryTranslate->slug ?? null,
            'images' => $newsPost->images->map(function ($image) {
                return [
                    'id' => $image->id,
                    'image_url' => $image->image_url
                        ? asset('storage/' . $image->image_url)
                        : asset('images/default_image.webp')
                ];
            }),
            'similar_news' => $similarNews,
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
            'message' => 'Error retrieving news post: ' . $e->getMessage(),
            'data' => null
        ], 500);
    }
}

}
