<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\Api\OurGoalResource;

use App\Http\Controllers\Controller;
use App\Interfaces\Services\Front\OurGoalServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use App\Traits\LocaleFromHeader;
use OpenApi\Annotations as OA;

class OurGoalController extends Controller
{
    use LocaleFromHeader;

    protected OurGoalServiceInterface $ourGoalService;

    public function __construct(OurGoalServiceInterface $ourGoalService)
    {
        $this->ourGoalService = $ourGoalService;
    }


   
    public function index(Request $request): JsonResponse
    {
        try {
            $languageId = $this->getLanguageIdFromHeader($request);
            $cacheKey = "api_our_goals_{$languageId}";
            
            $ourGoals = Cache::rememberForever($cacheKey, function () use ($languageId) {
                return $this->ourGoalService->getActiveOrderedWithTranslations($languageId);
            });

            return $this->success(OurGoalResource::collection($ourGoals), 'Our goals retrieved successfully');
        } catch (\Exception $e) {
            return $this->serverError('Error retrieving our goals: ' . $e->getMessage());
        }
    }
} 