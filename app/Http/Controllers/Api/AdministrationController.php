<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\Api\AdministrationResource;

use App\Http\Controllers\Controller;
use App\Interfaces\Services\Front\AdministrationServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use App\Traits\LocaleFromHeader;
use OpenApi\Annotations as OA;

class AdministrationController extends Controller
{
    use LocaleFromHeader;

    protected AdministrationServiceInterface $administrationService;

    public function __construct(AdministrationServiceInterface $administrationService)
    {
        $this->administrationService = $administrationService;
    }


    
    public function index(Request $request): JsonResponse
    {
        try {
            // Get language ID from header using trait
            $languageId = $this->getLanguageIdFromHeader($request);
            
            $cacheKey = "api_administrations_{$languageId}";
            
            $administrations = Cache::rememberForever($cacheKey, function () use ($languageId) {
                return $this->administrationService->getActiveOrderedWithTranslations($languageId);
            });

            return response()->json([
                'success' => true,
                'data' => AdministrationResource::collection($administrations)
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