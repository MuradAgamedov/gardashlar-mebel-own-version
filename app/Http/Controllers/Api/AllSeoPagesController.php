<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\AllSeoPagesRepository;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use OpenApi\Annotations as OA;

class AllSeoPagesController extends Controller
{
    protected $allSeoPagesRepository;

    public function __construct(AllSeoPagesRepository $allSeoPagesRepository)
    {
        $this->allSeoPagesRepository = $allSeoPagesRepository;
    }

    /**
     * @OA\Get(
     *     path="/api/all-seo-pages",
     *     operationId="getAllSeoPages",
     *     tags={"SEO"},
     *     summary="Get global SEO settings",
     *     description="Returns global SEO settings for all pages including indexing, following, header and footer code",
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="is_indexed", type="boolean", example=true),
     *                 @OA\Property(property="is_followed", type="boolean", example=true),
     *                 @OA\Property(property="page_header", type="string", example="<meta name=\"google-site-verification\" content=\"...\" />"),
     *                 @OA\Property(property="page_footer", type="string", example="<script>console.log('Footer script');</script>")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=500, description="Internal server error")
     * )
     */
    public function index(): JsonResponse
    {
        try {
            $cacheKey = "api:all-seo-pages:settings";

            $seoSettings = Cache::rememberForever($cacheKey, function () {
                $seoPages = $this->allSeoPagesRepository->get();
                
                return [
                    'is_indexed' => (bool) $seoPages->is_indexed,
                    'is_followed' => (bool) $seoPages->is_followed,
                    'page_header' => $seoPages->page_header,
                    'page_footer' => $seoPages->page_footer,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $seoSettings
            ]);

        } catch (\Throwable $e) {
            report($e);
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving SEO settings.',
                'data' => null
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/all-seo-pages/meta-tags",
     *     operationId="getSeoMetaTags",
     *     tags={"SEO"},
     *     summary="Get SEO meta tags for robots",
     *     description="Returns formatted meta tags for robots indexing and following",
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="robots_meta", type="string", example="index, follow"),
     *                 @OA\Property(property="robots_tag", type="string", example="<meta name=\"robots\" content=\"index, follow\" />"),
     *                 @OA\Property(property="is_indexed", type="boolean", example=true),
     *                 @OA\Property(property="is_followed", type="boolean", example=true)
     *             )
     *         )
     *     ),
     *     @OA\Response(response=500, description="Internal server error")
     * )
     */
    public function getMetaTags(): JsonResponse
    {
        try {
            $cacheKey = "api:all-seo-pages:meta-tags";

            $metaTags = Cache::rememberForever($cacheKey, function () {
                $seoPages = $this->allSeoPagesRepository->get();
                
                $indexValue = $seoPages->is_indexed ? 'index' : 'noindex';
                $followValue = $seoPages->is_followed ? 'follow' : 'nofollow';
                $robotsContent = $indexValue . ', ' . $followValue;
                
                return [
                    'robots_meta' => $robotsContent,
                    'robots_tag' => '<meta name="robots" content="' . $robotsContent . '" />',
                    'is_indexed' => (bool) $seoPages->is_indexed,
                    'is_followed' => (bool) $seoPages->is_followed,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $metaTags
            ]);

        } catch (\Throwable $e) {
            report($e);
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving SEO meta tags.',
                'data' => null
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/all-seo-pages/header-code",
     *     operationId="getSeoHeaderCode",
     *     tags={"SEO"},
     *     summary="Get SEO header code",
     *     description="Returns HTML/CSS/JS code for page header section",
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="header_code", type="string", example="<meta name=\"google-site-verification\" content=\"...\" />")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=500, description="Internal server error")
     * )
     */
    public function getHeaderCode(): JsonResponse
    {
        try {
            $cacheKey = "api:all-seo-pages:header-code";

            $headerCode = Cache::rememberForever($cacheKey, function () {
                $seoPages = $this->allSeoPagesRepository->get();
                
                return [
                    'header_code' => $seoPages->page_header,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $headerCode
            ]);

        } catch (\Throwable $e) {
            report($e);
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving SEO header code.',
                'data' => null
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/all-seo-pages/footer-code",
     *     operationId="getSeoFooterCode",
     *     tags={"SEO"},
     *     summary="Get SEO footer code",
     *     description="Returns HTML/CSS/JS code for page footer section",
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="footer_code", type="string", example="<script>console.log('Footer script');</script>")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=500, description="Internal server error")
     * )
     */
    public function getFooterCode(): JsonResponse
    {
        try {
            $cacheKey = "api:all-seo-pages:footer-code";

            $footerCode = Cache::rememberForever($cacheKey, function () {
                $seoPages = $this->allSeoPagesRepository->get();
                
                return [
                    'footer_code' => $seoPages->page_footer,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $footerCode
            ]);

        } catch (\Throwable $e) {
            report($e);
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving SEO footer code.',
                'data' => null
            ], 500);
        }
    }
}
