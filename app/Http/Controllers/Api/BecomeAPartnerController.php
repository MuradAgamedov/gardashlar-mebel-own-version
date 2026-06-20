<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Interfaces\Services\Front\BecomeAPartnerServiceInterface;
use App\Interfaces\Services\Front\BecomeAPartnerAdvantageServiceInterface;
use App\Http\Resources\Api\BecomeAPartnerResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use OpenApi\Annotations as OA;
use App\Traits\LocaleFromHeader;


class BecomeAPartnerController extends Controller
{
    use LocaleFromHeader;

    protected BecomeAPartnerServiceInterface $becomeAPartnerService;
    protected BecomeAPartnerAdvantageServiceInterface $becomeAPartnerAdvantageService;

    public function __construct(
        BecomeAPartnerServiceInterface $becomeAPartnerService,
        BecomeAPartnerAdvantageServiceInterface $becomeAPartnerAdvantageService
    ) {
        $this->becomeAPartnerService = $becomeAPartnerService;
        $this->becomeAPartnerAdvantageService = $becomeAPartnerAdvantageService;
    }

  
    public function index(Request $request): JsonResponse
    {
        try {
            $languageId = $this->getLanguageIdFromHeader($request);
            $cacheKey = "api_become_a_partner_{$languageId}";
            
            $data = Cache::rememberForever($cacheKey, function () use ($languageId) {
                // Get become a partner data using service
                $becomeAPartner = $this->becomeAPartnerService->first(['translates' => function($q) use ($languageId) {
                    $q->where('language_id', $languageId);
                }]);
                
                if (!$becomeAPartner) {
                    return null;
                }
                
                // Get active advantages with translations
                $advantages = $this->becomeAPartnerAdvantageService->getActiveOrderedWithTranslations($languageId);
                $becomeAPartner->setRelation('advantages', $advantages);
                
                return $becomeAPartner;
            });
            
            if (!$data) {
                return response()->json([
                    'success' => false,
                    'message' => 'Become a partner data not found',
                    'data' => null
                ], 404);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Become a partner data retrieved successfully',
                'data' => new BecomeAPartnerResource($data)
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving become a partner data: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }
}
