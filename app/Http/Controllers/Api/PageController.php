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
            $languageId = $this->getLanguageIdFromHeader($request);
    
            if (!$keyword) {
                return response()->json([
                    'success' => false,
                    'message' => 'Keyword parameter is required',
                    'data' => null
                ], 400);
            }
    
            $cacheKey = "api_page_keyword_{$keyword}_langid_{$languageId}";
    
            $page = Cache::rememberForever($cacheKey, function () use ($keyword, $languageId) {
                return Page::where('status', true)
                    ->where('keyword', $keyword)
                    ->with(['translates' => function ($q) use ($languageId) {
                        $q->where('language_id', $languageId);
                    }])
                    ->first();
            });
    
            if (!$page) {
                return response()->json([
                    'success' => false,
                    'message' => 'Page not found with this keyword',
                    'data' => null
                ], 404);
            }
    
            // Translate-i birbaşa page içində flat struktura çevirək
            $translate = $page->translates->first(); // yeganə tərcümə gəlir
    
            $responseData = [
                'id' => $page->id,
            ];
    
            if ($translate) {
                $responseData = array_merge($responseData, [
                    'title'           => $translate->title,
                    'slug'            => $translate->slug,
                    'seo_title'       => $translate->seo_title,
                    'seo_keywords'    => $translate->seo_keywords,
                    'seo_description' => $translate->seo_description,
                ]);
            }
    
            return response()->json([
                'success' => true,
                'data' => $responseData
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
