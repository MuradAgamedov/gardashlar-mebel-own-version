<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Interfaces\Services\Front\SuggestionsComplaintsServiceInterface;
use App\Traits\LocaleFromHeader;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="Suggestions & Complaints",
 *     description="API Endpoints for Suggestions & Complaints"
 * )
 */
class SuggestionsComplaintsController extends Controller
{
    use LocaleFromHeader;

    protected SuggestionsComplaintsServiceInterface $suggestionsComplaintsService;

    public function __construct(SuggestionsComplaintsServiceInterface $suggestionsComplaintsService)
    {
        $this->suggestionsComplaintsService = $suggestionsComplaintsService;
    }


    /**
     * @OA\Get(
     *     path="/api/suggestions-complaints",
     *     summary="Get suggestions and complaints information",
     *     description="Returns suggestions and complaints section data with translated content based on header language",
     *     tags={"Suggestions & Complaints"},
     *     @OA\Parameter(
     *         name="locale",
     *         in="header",
     *         description="Language ID (1=Az, 2=En, 3=Ru)",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Suggestions & Complaints retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Suggestions & Complaints retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="title", type="string", example="Your Opinions Are Valuable to Us"),
     *                 @OA\Property(property="description", type="string", example="Do you need the perfect venue for your project to launch successfully? We offer you the ideal venue with its location, amenities, and infrastructure!")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Suggestions & Complaints not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Suggestions & Complaints not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Error retrieving suggestions & complaints information")
     *         )
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        try {
            // Get language ID from header using trait
            $languageId = $this->getLanguageIdFromHeader($request);
            
            $cacheKey = "api_suggestions_complaints_{$languageId}";
            
            $suggestionsComplaints = Cache::rememberForever($cacheKey, function () use ($languageId) {
                $item = SuggestionsComplaints::getFirstOrCreate();
                
                $translation = $item->translates()->where('language_id', $languageId)->first();
                
                return [
                    'id' => $item->id,
                    'title' => $translation ? $translation->title : '',
                    'description' => $translation ? $translation->description : ''
                ];
            });

            if (!$suggestionsComplaints) {
                return response()->json([
                    'success' => false,
                    'message' => 'Suggestions & Complaints not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Suggestions & Complaints retrieved successfully',
                'data' => $suggestionsComplaints
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving suggestions & complaints information: ' . $e->getMessage()
            ], 500);
        }
    }
} 