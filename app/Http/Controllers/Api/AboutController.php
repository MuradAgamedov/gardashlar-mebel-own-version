<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Interfaces\Services\Front\AboutServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use App\Traits\LocaleFromHeader;
use OpenApi\Annotations as OA;

class AboutController extends Controller
{
    use LocaleFromHeader;

    protected AboutServiceInterface $aboutService;

    public function __construct(AboutServiceInterface $aboutService)
    {
        $this->aboutService = $aboutService;
    }

    public function index(Request $request): JsonResponse
    {
        try {
            // Get language ID from header using trait
            $languageId = $this->getLanguageIdFromHeader($request);
            
            $cacheKey = "api_abouts_{$languageId}";
            
            $abouts = Cache::rememberForever($cacheKey, function () use ($languageId) {
                return $this->aboutService->getActiveOrderedWithTranslations($languageId)
                    ->map(function ($about) {
                        $translation = $about->translates->first();
                        
                        return [
                            'id' => $about->id,
                            'image_url' => $about->image ? asset('storage/' . $about->image) : asset('images/default_image.webp'),
                            'date' => $about->date,
                            'title' => $translation ? $translation->title : '',
                            'description' => $translation ? $translation->description : '',
                            'image_alt_text' => $translation ? $translation->image_alt_text : null
                        ];
                    });
            });

            return response()->json([
                'success' => true,
                'data' => $abouts
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving about information: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }
} 