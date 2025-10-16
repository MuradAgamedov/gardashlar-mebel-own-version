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

}
