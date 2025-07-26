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
                    ->with(['translates' => function ($query) {
                        $query->where('status', true);
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
