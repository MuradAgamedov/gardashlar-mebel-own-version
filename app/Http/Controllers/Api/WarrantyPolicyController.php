<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WarrantyPolicy;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class WarrantyPolicyController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/warranty-policy",
     *     summary="Get warranty policy content",
     *     description="Returns warranty policy content with translated content based on header language",
     *     tags={"Warranty Policy"},
     *     @OA\Parameter(
     *         name="locale",
     *         in="header",
     *         description="Language ID (1=Az, 2=En, 3=Ru)",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Warranty policy retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Warranty policy retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="content", type="string", example="<p>Zəmanət siyasəti məzmunu...</p>")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Warranty policy not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Warranty policy not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Error retrieving warranty policy")
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

            // Get warranty policy with cache
            $warrantyPolicy = Cache::rememberForever("warranty_policy_{$languageId}", function () use ($languageId) {
                return WarrantyPolicy::with(['translates' => function($query) use ($languageId) {
                    $query->where('language_id', $languageId);
                }])->first();
            });

            if (!$warrantyPolicy) {
                return response()->json([
                    'success' => false,
                    'message' => 'Warranty policy not found'
                ], 404);
            }

            $translation = $warrantyPolicy->translates->first();

            $warrantyPolicyData = [
                'id' => $warrantyPolicy->id,
                'content' => $translation ? $translation->content : ''
            ];

            return response()->json([
                'success' => true,
                'message' => 'Warranty policy retrieved successfully',
                'data' => $warrantyPolicyData
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving warranty policy: ' . $e->getMessage()
            ], 500);
        }
    }
} 