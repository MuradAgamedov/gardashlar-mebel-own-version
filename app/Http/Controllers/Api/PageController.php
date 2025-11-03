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
                ->with(['translates.language'])
                ->get()
                ->map(function ($page) {
                    $translate = $page->translates->firstWhere('language_id', request()->header('Language-ID'));
                    
                    // slug obyektini qururuq
                    $slugTranslations = $page->translates->mapWithKeys(function ($t) {
                        return [$t->language->lang_code => $t->slug];
                    });

                    return [
                        'id'              => $page->id,
                        'keyword'         => $page->keyword,
                        'title'           => $translate->title ?? null,
                        'slug'            => $translate->slug ?? null,
                        'seo_title'       => $translate->seo_title ?? null,
                        'seo_keywords'    => $translate->seo_keywords ?? null,
                        'seo_description' => $translate->seo_description ?? null,
                        'opengraph_title' => $translate->opengraph_title ?? null,
                        'opengraph_description' => $translate->opengraph_description ?? null,
                        'opengraph_image' => $page->opengraph_image ? asset('storage/' . $page->opengraph_image) : null,
                        'opengraph_image_width' => $page->opengraph_image_width ?? null,
                        'opengraph_image_height' => $page->opengraph_image_height ?? null,
                        'opengraph_image_alt_text' => $translate->opengraph_image_alt_text ?? null,
                        "translates" => [
                            "slug" => $slugTranslations
                        ]
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
                    'opengraph_title' => $translate->opengraph_title ?? null,
                    'opengraph_description' => $translate->opengraph_description ?? null,
                    'opengraph_image' => $page->opengraph_image ? asset('storage/' . $page->opengraph_image) : null,
                    'opengraph_image_width' => $page->opengraph_image_width ?? null,
                    'opengraph_image_height' => $page->opengraph_image_height ?? null,
                    'opengraph_image_alt_text' => $translate->opengraph_image_alt_text ?? null,
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
