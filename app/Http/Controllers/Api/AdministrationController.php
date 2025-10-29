<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Administration;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use App\Traits\LocaleFromHeader;
use OpenApi\Annotations as OA;

class AdministrationController extends Controller
{
    use LocaleFromHeader;

    
    public function index(Request $request): JsonResponse
    {
        try {
            // Get language ID from header using trait
            $languageId = $this->getLanguageIdFromHeader($request);
            
            $cacheKey = "api_administrations_{$languageId}";
            
            $administrations = Cache::rememberForever($cacheKey, function () use ($languageId) {
                return Administration::with(['translates' => function($query) use ($languageId) {
                    $query->where('language_id', $languageId);
                }])
                ->where('status', true)
                ->orderBy('order')
                ->get()
                ->map(function ($administration) {
                    $translation = $administration->translates->first();
                    
                    return [
                        'id' => $administration->id,
                        'image_url' => $administration->image ? asset('storage/' . $administration->image) : asset('images/default_image.webp'),
                        'title' => $translation ? $translation->title : '',
                        'position' => $translation ? $translation->position : ''
                    ];
                });
            });

            return response()->json([
                'success' => true,
                'data' => $administrations
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving administration members: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }
} 