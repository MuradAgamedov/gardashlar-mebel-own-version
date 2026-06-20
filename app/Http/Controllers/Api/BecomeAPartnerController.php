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

/**
 * @OA\Tag(
 *     name="Become A Partner",
 *     description="API Endpoints for Become A Partner"
 * )
 */
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

    /**
     * @OA\Get(
     *     path="/api/become-a-partner",
     *     operationId="getBecomeAPartner",
     *     tags={"Become A Partner"},
     *     summary="Get become a partner data with advantages",
     *     description="Returns become a partner information with all active advantages",
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
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Become a partner data retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="content", type="string", example="Join our team and grow together..."),
     *                 @OA\Property(
     *                     property="advantages",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="icon", type="string", example="https://example.com/storage/become-a-partner-advantages/icon.svg"),
     *                         @OA\Property(property="title", type="string", example="Quality Products"),
     *                         @OA\Property(property="description", type="string", example="We offer the best quality products...")
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Become a partner data not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Become a partner data not found"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Error retrieving become a partner data"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     )
     * )
     */
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
