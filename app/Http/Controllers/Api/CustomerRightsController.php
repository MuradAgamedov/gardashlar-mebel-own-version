<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CustomerRights;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CustomerRightsController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/customer-rights",
     *     summary="Get customer rights content",
     *     description="Returns customer rights content with translated content based on header language",
     *     tags={"Customer Rights"},
     *     @OA\Parameter(
     *         name="locale",
     *         in="header",
     *         description="Language ID (1=Az, 2=En, 3=Ru)",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Customer rights retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Customer rights retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="content", type="string", example="<p>Müştəri hüquqları məzmunu...</p>")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Customer rights not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Customer rights not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Error retrieving customer rights")
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        try {
            // Get language ID from header
            $languageId = $request->header('locale', 1); // Default to Azerbaijani (1)
            
            // Validate language ID
            $language = Language::find($languageId);
            if (!$language) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid language ID'
                ], 400);
            }

            // Get customer rights with cache
            $customerRights = Cache::rememberForever("customer_rights_{$languageId}", function () use ($languageId) {
                return CustomerRights::with(['translates' => function($query) use ($languageId) {
                    $query->where('language_id', $languageId);
                }])->first();
            });

            if (!$customerRights) {
                return response()->json([
                    'success' => false,
                    'message' => 'Customer rights not found'
                ], 404);
            }

            $translation = $customerRights->translates->first();

            $customerRightsData = [
                'id' => $customerRights->id,
                'content' => $translation ? $translation->content : ''
            ];

            return response()->json([
                'success' => true,
                'message' => 'Customer rights retrieved successfully',
                'data' => $customerRightsData
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving customer rights: ' . $e->getMessage()
            ], 500);
        }
    }
} 