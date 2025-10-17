<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SeoPage;
use App\Models\Language;
use Illuminate\Http\Request;

class SeoPageController extends Controller
{
    /**
     * Get all SEO pages with title and slug only
     */
    public function index()
    {
        try {
            $seoPages = SeoPage::with(['translates' => function($query) {
                $query->select('seo_page_id', 'language_id', 'title', 'slug');
            }, 'translates.language:id,lang_code'])
            ->where('status', true)
            ->get(['id']);

            $result = [];
            foreach ($seoPages as $seoPage) {
                $pageData = ['id' => $seoPage->id];
                
                foreach ($seoPage->translates as $translate) {
                    $pageData['translations'][$translate->language->lang_code] = [
                        'title' => $translate->title,
                        'slug' => $translate->slug
                    ];
                }
                
                $result[] = $pageData;
            }

            return response()->json([
                'success' => true,
                'data' => $result
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching SEO pages',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get specific SEO page by slug and locale
     */
    public function show($slug, $locale = 'az')
    {
        try {
            // Get language by locale
            $language = Language::where('lang_code', $locale)->first();
            
            if (!$language) {
                return response()->json([
                    'success' => false,
                    'message' => 'Language not found'
                ], 404);
            }

            // Find SEO page by slug and language
            $seoPageTranslate = \App\Models\SeoPageTranslate::with(['seoPage', 'language'])
                ->where('slug', $slug)
                ->where('language_id', $language->id)
                ->first();

            if (!$seoPageTranslate || !$seoPageTranslate->seoPage->status) {
                return response()->json([
                    'success' => false,
                    'message' => 'SEO page not found'
                ], 404);
            }

            // Get category info if exists
            $categoryData = null;
            if ($seoPageTranslate->seoPage->category) {
                $categoryTranslate = $seoPageTranslate->seoPage->category
                    ->translates()
                    ->where('language_id', $language->id)
                    ->first();
                
                if ($categoryTranslate) {
                    $categoryData = [
                        'id' => $seoPageTranslate->seoPage->category->id,
                        'title' => $categoryTranslate->title,
                        'slug' => $categoryTranslate->slug ?? null
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $seoPageTranslate->seoPage->id,
                    'title' => $seoPageTranslate->title,
                    'slug' => $seoPageTranslate->slug,
                    'page_footer' => $seoPageTranslate->page_footer,
                    'meta_title' => $seoPageTranslate->meta_title,
                    'meta_description' => $seoPageTranslate->meta_description,
                    'meta_keywords' => $seoPageTranslate->meta_keywords,
                    'category' => $categoryData,
                    'language' => [
                        'code' => $language->lang_code,
                        'name' => $language->name
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching SEO page',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all SEO pages for specific locale
     */
    public function getByLocale($locale = 'az')
    {
        try {
            // Get language by locale
            $language = Language::where('lang_code', $locale)->first();
            
            if (!$language) {
                return response()->json([
                    'success' => false,
                    'message' => 'Language not found'
                ], 404);
            }

            $seoPages = SeoPage::with(['translates' => function($query) use ($language) {
                $query->where('language_id', $language->id)
                      ->select('seo_page_id', 'language_id', 'title', 'slug');
            }])
            ->where('status', true)
            ->get(['id']);

            $result = [];
            foreach ($seoPages as $seoPage) {
                if ($seoPage->translates->isNotEmpty()) {
                    $translate = $seoPage->translates->first();
                    $result[] = [
                        'id' => $seoPage->id,
                        'title' => $translate->title,
                        'slug' => $translate->slug
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'data' => $result,
                'locale' => $locale
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching SEO pages',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
