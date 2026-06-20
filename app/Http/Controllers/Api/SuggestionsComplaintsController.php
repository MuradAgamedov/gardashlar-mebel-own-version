<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\SuggestionsComplaintsResource;
use App\Interfaces\Services\Front\SuggestionsComplaintsServiceInterface;
use App\Traits\LocaleFromHeader;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use OpenApi\Annotations as OA;


class SuggestionsComplaintsController extends Controller
{
    use LocaleFromHeader;

    protected SuggestionsComplaintsServiceInterface $suggestionsComplaintsService;

    public function __construct(SuggestionsComplaintsServiceInterface $suggestionsComplaintsService)
    {
        $this->suggestionsComplaintsService = $suggestionsComplaintsService;
    }


    
    public function index(Request $request): JsonResponse
    {
        try {
            // Get language ID from header using trait
            $languageId = $this->getLanguageIdFromHeader($request);
            
            $cacheKey = "api_suggestions_complaints_{$languageId}";

            $suggestionsComplaints = Cache::rememberForever($cacheKey, function () use ($languageId) {
                return $this->suggestionsComplaintsService->first([
                    'translates' => fn($q) => $q->where('language_id', $languageId)
                ]);
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
                'data' => new SuggestionsComplaintsResource($suggestionsComplaints)
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving suggestions & complaints information: ' . $e->getMessage()
            ], 500);
        }
    }
} 