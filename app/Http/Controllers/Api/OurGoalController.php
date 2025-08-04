<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OurGoal;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use App\Traits\LocaleFromHeader;
use OpenApi\Annotations as OA;

class OurGoalController extends Controller
{
    use LocaleFromHeader;

    /**
     * @OA\Get(
     *     path="/api/our-goals",
     *     operationId="getOurGoals",
     *     tags={"Our Goals"},
     *     summary="Get all our goals",
     *     description="Returns a list of all active our goals, translated based on locale header",
     *     @OA\Parameter(name="locale", in="header", description="Language ID", required=false, @OA\Schema(type="integer", example=1)),
     *     @OA\Response(response=200, description="Successful response", @OA\JsonContent(type="object", @OA\Property(property="success", type="boolean", example=true), @OA\Property(property="data", type="array", @OA\Items(type="object", @OA\Property(property="id", type="integer", example=1), @OA\Property(property="title", type="string", example="Our Mission"), @OA\Property(property="description", type="string", example="To provide the best service to our customers"))))),
     *     @OA\Response(response=500, description="Internal server error", @OA\JsonContent(type="object", @OA\Property(property="success", type="boolean", example=false), @OA\Property(property="message", type="string", example="Error retrieving our goals")))
     * )
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $languageId = $this->getLanguageIdFromHeader($request);
            $cacheKey = "api_our_goals_{$languageId}";
            
            $ourGoals = Cache::rememberForever($cacheKey, function () use ($languageId) {
                return OurGoal::with(['translates' => function($query) use ($languageId) {
                    $query->where('language_id', $languageId);
                }])
                ->where('status', true)
                ->orderBy('order')
                ->get()
                ->map(function ($ourGoal) {
                    $translation = $ourGoal->translates->first();
                    return [
                        'id' => $ourGoal->id,
                        'title' => $translation ? $translation->title : '',
                        'description' => $translation ? $translation->description : ''
                    ];
                });
            });

            return response()->json(['success' => true, 'data' => $ourGoals], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error retrieving our goals: ' . $e->getMessage(), 'data' => []], 500);
        }
    }
} 