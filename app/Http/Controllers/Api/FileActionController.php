<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use OpenApi\Annotations as OA;

class FileActionController extends Controller
{
   
    public function download(Request $request): Response|JsonResponse|BinaryFileResponse
    {
        try {
            $url = $request->query('url');
            
            if (!$url) {
                return response()->json([
                    'success' => false,
                    'message' => 'URL parameter is required'
                ], 400);
            }

            // URL-in düzgünlüyünü yoxlayırıq
            if (!filter_var($url, FILTER_VALIDATE_URL)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid URL format'
                ], 400);
            }

            // URL-in bizim domain-dən olduğunu yoxlayırıq
            $parsedUrl = parse_url($url);
            $allowedHosts = [
                'admin.gardaslar.coder.az',
                'admin.gardashlar.com',
                'localhost',
                '127.0.0.1',
                parse_url(config('app.url'), PHP_URL_HOST)
            ];

            if (!in_array($parsedUrl['host'] ?? '', $allowedHosts)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied. Only files from authorized domains can be downloaded.'
                ], 403);
            }

            // URL-dən storage path-i çıxarırıq
            $path = $this->extractStoragePath($url);
            
            if (!$path) {
                return response()->json([
                    'success' => false,
                    'message' => 'Could not extract file path from URL'
                ], 400);
            }

            // Faylın mövcudluğunu yoxlayırıq
            if (!Storage::disk('public')->exists($path)) {
                return response()->json([
                    'success' => false,
                    'message' => 'File not found',
                    'debug' => [
                        'requested_url' => $url,
                        'extracted_path' => $path,
                        'storage_path' => Storage::disk('public')->path($path),
                        'storage_exists' => Storage::disk('public')->exists($path)
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
                'message' => 'Error downloading file: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * URL-dən storage path-i çıxarır
     */
    private function extractStoragePath(string $url): ?string
    {
        // URL-də /storage/ hissəsini axtarırıq
        if (strpos($url, '/storage/') !== false) {
            $parts = explode('/storage/', $url);
            if (isset($parts[1])) {
                return $parts[1];
            }
        }

        // Əgər /storage/ yoxdursa, URL-in son hissəsini götürürük
        $parsedUrl = parse_url($url);
        $path = $parsedUrl['path'] ?? '';
        
        // Başlanğıc / işarəsini silirik
        $path = ltrim($path, '/');
        
        return $path;
    }
}
