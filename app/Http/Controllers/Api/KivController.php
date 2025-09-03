<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kiv;
use App\Models\KivCategory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
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
     *     @OA\Parameter(
     *         name="locale",
     *         in="header",
     *         description="Language ID (1=En, 3=Az)",
     *         required=true,
     *         @OA\Schema(type="integer", example=3)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="KIVs retrieved successfully"),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No KIVs found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="No KIVs found"),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Error retrieving KIVs"),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *         )
     *     )
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

    /**
     * @OA\Get(
     *     path="/api/kivs/download",
     *     operationId="downloadKivImage",
     *     tags={"KIVs"},
     *     summary="Download KIV image",
     *     description="Downloads a KIV image by providing the image path",
     *     @OA\Parameter(
     *         name="path",
     *         in="query",
     *         description="Image path (e.g., kivs/filename.png)",
     *         required=true,
     *         @OA\Schema(type="string", example="kivs/3eb74a1ab26761dc54a05d7e3de374790d39fe52_1754390181_p0aDbClx.png")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Image downloaded successfully",
     *         @OA\MediaType(
     *             mediaType="image/*",
     *             @OA\Schema(type="string", format="binary")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Image not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Image not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid path provided",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Invalid path provided")
     *         )
     *     )
     * )
     */
    public function download(Request $request): Response|JsonResponse|BinaryFileResponse
    {
        try {
            $path = $request->query('path');
            
            if (!$path) {
                return response()->json([
                    'success' => false,
                    'message' => 'Path parameter is required'
                ], 400);
            }

            // Təhlükəsizlik üçün path-i təmizləyirik
            $path = ltrim($path, '/');
            
            // Yalnız kivs qovluğundan faylları yükləməyə icazə veririk
            if (!str_starts_with($path, 'kivs/')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied. Only KIV images can be downloaded.'
                ], 403);
            }

            // Faylın mövcudluğunu yoxlayırıq
            if (!Storage::disk('public')->exists($path)) {
                // Debug məlumatları əlavə edirik
                $storagePath = Storage::disk('public')->path($path);
                $allFiles = Storage::disk('public')->allFiles('kivs');
                
                return response()->json([
                    'success' => false,
                    'message' => 'Image not found',
                    'debug' => [
                        'requested_path' => $path,
                        'storage_path' => $storagePath,
                        'available_files' => $allFiles,
                        'storage_exists' => file_exists($storagePath)
                    ]
                ], 404);
            }

            // Faylın tam yolunu alırıq
            $fullPath = Storage::disk('public')->path($path);
            
            // MIME type-ı təyin edirik
            $mimeType = mime_content_type($fullPath);
            
            // Fayl adını alırıq
            $filename = basename($path);
            
            // Faylı yükləyirik
            return response()->file($fullPath, [
                'Content-Type' => $mimeType,
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Access-Control-Allow-Origin' => '*',
                'Access-Control-Allow-Methods' => 'GET',
                'Access-Control-Allow-Headers' => 'Content-Type',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error downloading image: ' . $e->getMessage()
            ], 500);
        }
    }
}
