<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kiv;
use App\Models\KivCategory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(name="KIVs", description="API Endpoints for KIVs")
 */
class KivController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/kivs",
     *     operationId="getKivs",
     *     tags={"KIVs"},
     *     summary="Get KIVs grouped by categories",
     *     description="Returns KIVs organized by categories with translations",
     *     @OA\Parameter(name="locale", in="header", description="Language ID (1=En, 3=Az)", required=true, @OA\Schema(type="integer", example=3)),
     *     @OA\Response(response=200, description="Successful response", @OA\JsonContent(type="object", @OA\Property(property="success", type="boolean", example=true), @OA\Property(property="message", type="string", example="KIVs retrieved successfully"), @OA\Property(property="data", type="array", @OA\Items(type="object", @OA\Property(property="id", type="integer", example=1), @OA\Property(property="title", type="string", example="Category Title"), @OA\Property(property="kivs", type="array", @OA\Items(type="object", @OA\Property(property="id", type="integer", example=1), @OA\Property(property="image", type="string", example="https://example.com/storage/kivs/image.jpg"), @OA\Property(property="video_id", type="string", example="RACNuuviPcM"), @OA\Property(property="video_link", type="string", example="https://www.youtube.com/watch?v=RACNuuviPcM"))))))),
     *     @OA\Response(response=404, description="No KIVs found", @OA\JsonContent(type="object", @OA\Property(property="success", type="boolean", example=false), @OA\Property(property="message", type="string", example="No KIVs found"), @OA\Property(property="data", type="array"))),
     *     @OA\Response(response=500, description="Internal server error", @OA\JsonContent(type="object", @OA\Property(property="success", type="boolean", example=false), @OA\Property(property="message", type="string", example="Error retrieving KIVs"), @OA\Property(property="data", type="array")))
     * )
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $languageId = $request->header('locale', 3);
            $cacheKey = "api_kivs_{$languageId}";
            
            $data = Cache::rememberForever($cacheKey, function () use ($languageId) {
                $categories = KivCategory::with(['translates' => function($query) use ($languageId) {
                    $query->where('language_id', $languageId);
                }, 'kivs' => function($query) {
                    $query->where('status', true)->orderBy('order');
                }])->where('status', true)->orderBy('order')->get();
                
                if ($categories->isEmpty()) {
                    return [];
                }
                
                return $categories->map(function ($category) use ($languageId) {
                    $translation = $category->translates->first();
                    
                    $kivs = $category->kivs->map(function ($kiv) {
                        $videoId = $this->extractYouTubeVideoId($kiv->video_link);
                        
                        return [
                            'id' => $kiv->id,
                            'image' => $kiv->image ? asset('storage/' . $kiv->image) : null,
                            'video_id' => $videoId,
                            'video_link' => $kiv->video_link,
                        ];
                    });
                    
                    return [
                        'id' => $category->id,
                        'title' => $translation ? $translation->title : '',
                        'kivs' => $kivs
                    ];
                });
            });
            
            if (empty($data)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No KIVs found',
                    'data' => []
                ], 404);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'KIVs retrieved successfully',
                'data' => $data
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving KIVs: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }

    /**
     * Extract YouTube video ID from various YouTube URL formats
     */
    private function extractYouTubeVideoId(?string $url): ?string
    {
        if (!$url) {
            return null;
        }

        $patterns = [
            '/youtube\.com\/watch\?v=([a-zA-Z0-9_-]+)/',
            '/youtu\.be\/([a-zA-Z0-9_-]+)/',
            '/youtube\.com\/embed\/([a-zA-Z0-9_-]+)/',
            '/youtube\.com\/v\/([a-zA-Z0-9_-]+)/',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $url, $matches)) {
                return $matches[1];
            }
        }

        return null;
    }
}
