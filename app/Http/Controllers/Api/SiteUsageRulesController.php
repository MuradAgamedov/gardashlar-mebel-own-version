<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\LocaleFromHeader;
use App\Interfaces\Services\Front\SiteUsageRulesServiceInterface;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SiteUsageRulesController extends Controller
{
    use LocaleFromHeader;

    protected SiteUsageRulesServiceInterface $siteUsageRulesService;

    public function __construct(SiteUsageRulesServiceInterface $siteUsageRulesService)
    {
        $this->siteUsageRulesService = $siteUsageRulesService;
    }

    /**
     * @OA\Get(
     *     path="/api/site-usage-rules",
     *     summary="Get site usage rules content",
     *     description="Returns site usage rules content with translated content based on header language",
     *     tags={"Site Usage Rules"},
     *     @OA\Parameter(
     *         name="locale",
     *         in="header",
     *         description="Language ID (1=Az, 2=En, 3=Ru)",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Site usage rules retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Site usage rules retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="content", type="string", example="<p>Sayt istifadə qaydaları məzmunu...</p>")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Site usage rules not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Site usage rules not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Error retrieving site usage rules")
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        try {
            // Get language ID from header
            $languageId = $this->getLanguageIdFromHeader($request); // Default to Azerbaijani (1)
            
            // Validate language ID
            $language = Language::find($languageId);
            if (!$language) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid language ID'
                ], 400);
            }

            // Get site usage rules with cache
            $siteUsageRules = Cache::rememberForever("site_usage_rules_{$languageId}", function () use ($languageId) {
                return SiteUsageRules::with(['translates' => function($query) use ($languageId) {
                    $query->where('language_id', $languageId);
                }])->first();
            });

            if (!$siteUsageRules) {
                return response()->json([
                    'success' => false,
                    'message' => 'Site usage rules not found'
                ], 404);
            }

            $translation = $siteUsageRules->translates->first();

            $siteUsageRulesData = [
                'id' => $siteUsageRules->id,
                'content' => $translation ? $translation->content : ''
            ];

            return response()->json([
                'success' => true,
                'message' => 'Site usage rules retrieved successfully',
                'data' => $siteUsageRulesData
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving site usage rules: ' . $e->getMessage()
            ], 500);
        }
    }
} 