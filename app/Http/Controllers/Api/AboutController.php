<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Interfaces\Services\Front\AboutServiceInterface;
use App\Http\Resources\Api\AboutResource;
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
                return $this->aboutService->getActiveOrderedWithTranslations($languageId);
            });

            return $this->success(AboutResource::collection($abouts));

        } catch (\Exception $e) {
            return $this->serverError('Error retrieving about information: ' . $e->getMessage());
        }
    }
} 