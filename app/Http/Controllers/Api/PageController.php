<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use App\Traits\LocaleFromHeader;
use OpenApi\Annotations as OA;

class PageController extends Controller
{
    use LocaleFromHeader;

   
public function index(Request $request): JsonResponse
{
    try {
        $languageId = $this->getLanguageIdFromHeader($request);
        $cacheKey = "api:pages:all:lang:{$languageId}";

        $pages = Cache::rememberForever($cacheKey, function () {
            return Page::where('status', true)
                ->with(['translates.language']) // language modelində 'code' var
                ->get()
                ->map(function ($page) {
                    $translations = $page->translates->mapWithKeys(function ($translate) {
                        return [$translate->language->code => [
                            'title' => $translate->title,
                            'slug' => $translate->slug,
                            'seo_title' => $translate->seo_title,
                            'seo_keywords' => $translate->seo_keywords,
                            'seo_description' => $translate->seo_description,
                            'opengraph_title' => $translate->opengraph_title,
                            'opengraph_description' => $translate->opengraph_description,
                            'opengraph_image_alt_text' => $translate->opengraph_image_alt_text,
                        ]];
                    });

                    return [
                        'id' => $page->id,
                        'keyword' => $page->keyword,
                        'slug' => $translations->map(fn($t) => $t['slug']),
                        'translations' => $translations,
                        'opengraph_image' => $page->opengraph_image ? asset('storage/' . $page->opengraph_image) : null,
                        'opengraph_image_width' => $page->opengraph_image_width,
                        'opengraph_image_height' => $page->opengraph_image_height,
                    ];
                });
        });

        return response()->json([
            'success' => true,
            'data' => $pages
        ]);
    } catch (\Throwable $e) {
        report($e);
        return response()->json([
            'success' => false,
            'message' => 'Error retrieving pages.',
            'data' => []
        ], 500);
    }
}


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

        $responseData = Cache::rememberForever($cacheKey, function () use ($keyword) {
            $page = Page::where('status', true)
                ->where('keyword', $keyword)
                ->with(['translates.language'])
                ->first();

            if (!$page) {
                return null;
            }

            // bütün dillərə görə tərcümələri xəritələyirik
            $translations = $page->translates->mapWithKeys(function ($translate) {
                return [$translate->language->code => [
                    'title' => $translate->title,
                    'slug' => $translate->slug,
                    'seo_title' => $translate->seo_title,
                    'seo_keywords' => $translate->seo_keywords,
                    'seo_description' => $translate->seo_description,
                    'opengraph_title' => $translate->opengraph_title,
                    'opengraph_description' => $translate->opengraph_description,
                    'opengraph_image_alt_text' => $translate->opengraph_image_alt_text,
                ]];
            });

            return [
                'id' => $page->id,
                'keyword' => $page->keyword,
                'slug' => $translations->map(fn($t) => $t['slug']),
                'translations' => $translations,
                'opengraph_image' => $page->opengraph_image ? asset('storage/' . $page->opengraph_image) : null,
                'opengraph_image_width' => $page->opengraph_image_width,
                'opengraph_image_height' => $page->opengraph_image_height,
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
        report($e);
        return response()->json([
            'success' => false,
            'message' => 'Error retrieving page.',
            'data' => null
        ], 500);
    }
}

}
