<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BecomeAInvestor;
use App\Models\BecomeAInvestorAdvantage;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(name="Become A Investor", description="API Endpoints for Become A Investor")
 */
class BecomeAInvestorController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/become-a-investor",
     *     operationId="getBecomeAInvestor",
     *     tags={"Become A Investor"},
     *     summary="Get become a investor data with advantages",
     *     description="Returns become a investor information with all active advantages",
     *     @OA\Parameter(name="locale", in="header", description="Language ID (1=En, 3=Az)", required=true, @OA\Schema(type="integer", example=3)),
     *     @OA\Response(response=200, description="Successful response", @OA\JsonContent(type="object", @OA\Property(property="success", type="boolean", example=true), @OA\Property(property="message", type="string", example="Become a investor data retrieved successfully"), @OA\Property(property="data", type="object", @OA\Property(property="id", type="integer", example=1), @OA\Property(property="content", type="string", example="Invest in our company and grow your wealth..."), @OA\Property(property="advantages", type="array", @OA\Items(type="object", @OA\Property(property="id", type="integer", example=1), @OA\Property(property="icon", type="string", example="https://example.com/storage/become-a-investor-advantages/icon.svg"), @OA\Property(property="title", type="string", example="High Returns"), @OA\Property(property="description", type="string", example="Get high returns on your investment..."), @OA\Property(property="icon_alt_text", type="string", example="High returns icon"))))),
     *     @OA\Response(response=404, description="Become a investor data not found", @OA\JsonContent(type="object", @OA\Property(property="success", type="boolean", example=false), @OA\Property(property="message", type="string", example="Become a investor data not found"), @OA\Property(property="data", type="object"))),
     *     @OA\Response(response=500, description="Internal server error", @OA\JsonContent(type="object", @OA\Property(property="success", type="boolean", example=false), @OA\Property(property="message", type="string", example="Error retrieving become a investor data"), @OA\Property(property="data", type="object")))
     * )
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $languageId = $request->header('locale', 3);
            $cacheKey = "api_become_a_investor_{$languageId}";
            
            $data = Cache::rememberForever($cacheKey, function () use ($languageId) {
                $becomeAInvestor = BecomeAInvestor::first();
                
                if (!$becomeAInvestor) {
                    return null;
                }

                $translation = $becomeAInvestor->translates()->where('language_id', $languageId)->first();

                $advantages = BecomeAInvestorAdvantage::with(['translates' => function($query) use ($languageId) {
                    $query->where('language_id', $languageId);
                }])->where('status', true)->orderBy('order')->get()->map(function ($advantage) {
                    $translation = $advantage->translates->first();
                    
                    return [
                        'id' => $advantage->id,
                        'icon' => $advantage->icon ? asset('storage/' . $advantage->icon) : null,
                        'title' => $translation ? $translation->title : '',
                        'description' => $translation ? $translation->description : '',
                        'icon_alt_text' => $translation ? $translation->icon_alt_text : ''
                    ];
                });

                return [
                    'id' => $becomeAInvestor->id,
                    'content' => $translation ? $translation->content : '',
                    'advantages' => $advantages
                ];
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
                'data' => $data
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
