<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Interfaces\Services\Front\BecomeAInvestorServiceInterface;
use App\Interfaces\Services\Front\BecomeAInvestorAdvantageServiceInterface;
use App\Http\Resources\Api\BecomeAInvestorResource;
use App\Traits\LocaleFromHeader;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use OpenApi\Annotations as OA;


class BecomeAInvestorController extends Controller
{
    use LocaleFromHeader;

    protected BecomeAInvestorServiceInterface $becomeAInvestorService;
    protected BecomeAInvestorAdvantageServiceInterface $becomeAInvestorAdvantageService;

    public function __construct(
        BecomeAInvestorServiceInterface $becomeAInvestorService,
        BecomeAInvestorAdvantageServiceInterface $becomeAInvestorAdvantageService
    ) {
        $this->becomeAInvestorService = $becomeAInvestorService;
        $this->becomeAInvestorAdvantageService = $becomeAInvestorAdvantageService;
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $languageId = $this->getLanguageIdFromHeader($request);
            $cacheKey = "api_become_a_investor_{$languageId}";
            
            $data = Cache::rememberForever($cacheKey, function () use ($languageId) {
                $becomeAInvestor = $this->becomeAInvestorService->first(['translates' => function($q) use ($languageId) {
                    $q->where('language_id', $languageId);
                }]);
                
                if (!$becomeAInvestor) {
                    return null;
                }

                $advantages = $this->becomeAInvestorAdvantageService->getActiveOrderedWithTranslations($languageId);
                $becomeAInvestor->setRelation('advantages', $advantages);

                return $becomeAInvestor;
            });

            if (!$data) {
                return response()->json([
                    'success' => false,
                    'message' => 'Become a investor data not found',
                    'data' => null
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Become a investor data retrieved successfully',
                'data' => new BecomeAInvestorResource($data)
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving become a investor data: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }
}
