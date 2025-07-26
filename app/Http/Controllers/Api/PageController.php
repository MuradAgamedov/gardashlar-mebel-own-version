<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use App\Traits\LocaleFromHeader;

class PageController extends Controller
{
    use LocaleFromHeader;

    /**
     * Get page by keyword
     */
    public function findByKeyword(Request $request, $keyword): JsonResponse
    {
        try {
            // Set locale from header
            $this->setLocaleFromHeader($request);
            
            if (!$keyword) {
                return response()->json([
                    'success' => false,
                    'message' => 'Keyword parameter is required',
                    'data' => null
                ], 400);
            }

            $page = Cache::rememberForever("api_page_keyword_{$keyword}", function () use ($keyword) {
                return Page::where('status', true)
                    ->where('keyword', $keyword)
                    ->first();
            });

            if (!$page) {
                return response()->json([
                    'success' => false,
                    'message' => 'Page not found with this keyword',
                    'data' => null
                ], 404);
            }

            // Get translation for current locale
            $currentLocale = $this->getLocaleFromHeader($request);
            $translation = $page->translates()->where('language_id', function($query) use ($currentLocale) {
                $query->select('id')->from('languages')->where('lang_code', $currentLocale);
            })->first();

            // Add translated fields to page object
            $pageData = $page->toArray();
            if ($translation) {
                $pageData['title'] = $translation->title;
                $pageData['slug'] = $translation->slug;
                $pageData['content'] = $translation->content;
                $pageData['meta_title'] = $translation->meta_title;
                $pageData['meta_description'] = $translation->meta_description;
            }

            return response()->json([
                'success' => true,
                'data' => $pageData
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving page: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }
}
