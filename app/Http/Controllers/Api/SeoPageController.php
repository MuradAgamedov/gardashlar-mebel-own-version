<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SeoPage;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use App\Traits\LocaleFromHeader;

class SeoPageController extends Controller
{
    use LocaleFromHeader;

    /**
     * Get all SEO pages with title and slug only
     */
    public function index(Request $request): JsonResponse
    {
        try {
            // Get language ID from header using trait
            $languageId = $this->getLanguageIdFromHeader($request);
            
            $cacheKey = "api_seo_pages_{$languageId}";
            
            $seoPages = Cache::rememberForever($cacheKey, function () use ($languageId) {
                return SeoPage::with(['translates' => function($query) use ($languageId) {
                    $query->where('language_id', $languageId)
                          ->select('seo_page_id', 'language_id', 'title', 'slug');
                }])
                ->where('status', true)
                ->get(['id'])
                ->map(function ($seoPage) {
                    $translation = $seoPage->translates->first();
                    
                    return [
                        'id' => $seoPage->id,
                        'title' => $translation ? $translation->title : '',
                        'slug' => $translation ? $translation->slug : ''
                    ];
                })
                ->filter(function ($item) {
                    return !empty($item['title']) && !empty($item['slug']);
                })
                ->values();
            });

            return response()->json([
                'success' => true,
                'data' => $seoPages
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching SEO pages: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }

    /**
     * Get specific SEO page by slug
     */
    public function show(Request $request, $slug): JsonResponse
    {
        try {
            // Get language ID from header using trait
            $languageId = $this->getLanguageIdFromHeader($request);
            
            $cacheKey = "api_seo_page_{$slug}_{$languageId}";
            
            $seoPageData = Cache::rememberForever($cacheKey, function () use ($slug, $languageId) {
                // Find SEO page by slug and language
                $seoPageTranslate = \App\Models\SeoPageTranslate::with(['seoPage.category.translates', 'language'])
                    ->where('slug', $slug)
                    ->where('language_id', $languageId)
                    ->first();

                if (!$seoPageTranslate || !$seoPageTranslate->seoPage->status) {
                    return null;
                }

                // Get category info if exists
                $categoryData = null;
                if ($seoPageTranslate->seoPage->category) {
                    $categoryTranslate = $seoPageTranslate->seoPage->category
                        ->translates()
                        ->where('language_id', $languageId)
                        ->first();
                    
                    if ($categoryTranslate) {
                        $categoryData = [
                            'id' => $seoPageTranslate->seoPage->category->id,
                            'title' => $categoryTranslate->title,
                            'slug' => $categoryTranslate->slug ?? null
                        ];
                    }
                }

                return [
                    'id' => $seoPageTranslate->seoPage->id,
                    'title' => $seoPageTranslate->title,
                    'slug' => $seoPageTranslate->slug,
                    'page_footer' => $seoPageTranslate->page_footer,
                    'meta_title' => $seoPageTranslate->meta_title,
                    'meta_description' => $seoPageTranslate->meta_description,
                    'meta_keywords' => $seoPageTranslate->meta_keywords,
                    'category' => $categoryData,
                    'language' => [
                        'code' => $seoPageTranslate->language->lang_code,
                        'name' => $seoPageTranslate->language->name
                    ]
                ];
            });

            if (!$seoPageData) {
                return response()->json([
                    'success' => false,
                    'message' => 'SEO page not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $seoPageData
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching SEO page: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }

}
