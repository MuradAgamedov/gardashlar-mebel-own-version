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

    /**
     * @OA\Get(
     *     path="/api/pages",
     *     operationId="getAllPages",
     *     tags={"Pages"},
     *     summary="Get all pages",
     *     description="Returns all translated pages based on locale (from header)",
     *     @OA\Header(
     *         header="locale",
     *         description="Language locale code (e.g., az, en, ru)",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="keyword", type="string", example="about-us"),
     *                     @OA\Property(property="title", type="string", example="Haqqımızda"),
     *                     @OA\Property(property="slug", type="string", example="haqqimizda"),
     *                     @OA\Property(property="seo_title", type="string", example="Haqqımızda"),
     *                     @OA\Property(property="seo_keywords", type="string", example="Haqqımızda, Bizim haqqımızda"),
     *                     @OA\Property(property="seo_description", type="string", example="Şirkət haqqında ümumi məlumat")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error"
     *     )
     * )
     * Get all pages
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $languageId = $this->getLanguageIdFromHeader($request);
            $cacheKey = "api:pages:all:lang:{$languageId}";

            $pages = Cache::rememberForever($cacheKey, function () use ($languageId) {
                return Page::where('status', true)
                    ->with(['translates' => fn($q) => $q->where('language_id', $languageId)])
                    ->get()
                    ->map(function ($page) {
                        $translate = $page->translates->first();
                        
                        return [
                            'id'              => $page->id,
                            'keyword'         => $page->keyword,
                            'title'           => $translate->title ?? null,
                            'slug'            => $translate->slug ?? null,
                            'seo_title'       => $translate->seo_title ?? null,
                            'seo_keywords'    => $translate->seo_keywords ?? null,
                            'seo_description' => $translate->seo_description ?? null,
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

    /**
     * @OA\Get(
     *     path="/api/pages/{keyword}",
     *     operationId="getPageByKeyword",
     *     tags={"Pages"},
     *     summary="Get a page by keyword",
     *     description="Returns a translated page by keyword and locale (based on header)",
     *     @OA\Parameter(
     *         name="keyword",
     *         in="path",
     *         description="The keyword of the page",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Header(
     *         header="locale",
     *         description="Language locale code (e.g., az, en, ru)",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=2),
     *                 @OA\Property(property="title", type="string", example="Haqqımızda"),
     *                 @OA\Property(property="slug", type="string", example="haqqimizda"),
     *                 @OA\Property(property="seo_title", type="string", example="Haqqımızda"),
     *                 @OA\Property(property="seo_keywords", type="string", example="Haqqımızda, Bizim haqqımızda"),
     *                 @OA\Property(property="seo_description", type="string", example="Şirkət haqqında ümumi məlumat")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Keyword parameter is required"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Page not found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error"
     *     )
     * )
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
