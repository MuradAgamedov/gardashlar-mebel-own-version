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
        if (empty($keyword)) {
            return response()->json([
                'success' => false,
                'message' => 'Keyword parameter is required',
                'data' => null
            ], 400);
        }

        try {
            $languageId = $this->getLanguageIdFromHeader($request);
            $cacheKey = "api:page:keyword:{$keyword}:lang:{$languageId}";

            $responseData = Cache::rememberForever($cacheKey, function () use ($keyword, $languageId) {
                $page = Page::where('status', true)
                    ->where('keyword', $keyword)
                    ->with(['translates' => fn($q) => $q->where('language_id', $languageId)])
                    ->first();

                if (!$page) {
                    return null;
                }

                $translate = $page->translates->first();

                return [
                    'id'              => $page->id,
                    'title'           => $translate->title ?? null,
                    'slug'            => $translate->slug ?? null,
                    'seo_title'       => $translate->seo_title ?? null,
                    'seo_keywords'    => $translate->seo_keywords ?? null,
                    'seo_description' => $translate->seo_description ?? null,
                ];
            });

            if (!$responseData) {
                return response()->json([
                    'success' => false,
                    'message' => 'Page not found with this keyword',
                    'data' => null
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $responseData
            ]);

        } catch (\Throwable $e) {
            report($e); // Optional: logs the error
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving page.',
                'data' => null
            ], 500);
        }
    }
}
