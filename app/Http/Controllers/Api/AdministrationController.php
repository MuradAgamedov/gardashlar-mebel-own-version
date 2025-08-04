<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Administration;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use App\Traits\LocaleFromHeader;
use OpenApi\Annotations as OA;

class AdministrationController extends Controller
{
    use LocaleFromHeader;

    /**
     * @OA\Get(
     *     path="/api/administrations",
     *     operationId="getAdministrations",
     *     tags={"Administrations"},
     *     summary="Get all administration members",
     *     description="Returns a list of all active administration members, translated based on locale header",
     *     @OA\Parameter(
     *         name="locale",
     *         in="header",
     *         description="Language ID (e.g., 1 for Azerbaijani, 2 for English)",
     *         required=false,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="image_url", type="string", example="https://example.com/storage/administrations/image.jpg", nullable=true),
     *                     @OA\Property(property="title", type="string", example="John Doe"),
     *                     @OA\Property(property="position", type="string", example="CEO"),
     *                     @OA\Property(property="order", type="integer", example=1)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Error retrieving administration members")
     *         )
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        try {
            // Get language ID from header using trait
            $languageId = $this->getLanguageIdFromHeader($request);
            
            $cacheKey = "api_administrations_{$languageId}";
            
            $administrations = Cache::rememberForever($cacheKey, function () use ($languageId) {
                return Administration::with(['translates' => function($query) use ($languageId) {
                    $query->where('language_id', $languageId);
                }])
                ->where('status', true)
                ->orderBy('order')
                ->get()
                ->map(function ($administration) {
                    $translation = $administration->translates->first();
                    
                    return [
                        'id' => $administration->id,
                        'image_url' => $administration->image ? asset('storage/' . $administration->image) : null,
                        'title' => $translation ? $translation->title : '',
                        'position' => $translation ? $translation->position : '',
                        'order' => $administration->order
                    ];
                });
            });

            return response()->json([
                'success' => true,
                'data' => $administrations
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