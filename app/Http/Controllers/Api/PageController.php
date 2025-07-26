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
            // Get language_id from header
            $languageId = $this->getLanguageIdFromHeader($request);

            if (!$keyword) {
                return response()->json([
                    'success' => false,
                    'message' => 'Keyword parameter is required',
                    'data' => null
                ], 400);
            }

            // Cache açarını language_id ilə fərqləndir
            $page = Cache::rememberForever("api_page_keyword_{$keyword}_langid_{$languageId}", function () use ($keyword, $languageId) {
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

            return response()->json([
                'success' => true,
                'data' => $page
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
