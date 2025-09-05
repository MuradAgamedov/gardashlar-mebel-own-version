<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\LocaleFromHeader;
use App\Models\RefundPolicy;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class RefundPolicyController extends Controller
{
    use LocaleFromHeader;
    /**
     * @OA\Get(
     *     path="/api/refund-policy",
     *     summary="Get refund policy content",
     *     description="Returns refund policy content with translated content based on header language",
     *     tags={"Refund Policy"},
     *     @OA\Parameter(
     *         name="locale",
     *         in="header",
     *         description="Language ID (1=Az, 2=En, 3=Ru)",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Refund policy retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Refund policy retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="content", type="string", example="<p>Geri qaytarma siyasəti məzmunu...</p>")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Refund policy not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Refund policy not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Error retrieving refund policy")
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

            // Get refund policy with cache
            $refundPolicy = Cache::rememberForever("refund_policy_{$languageId}", function () use ($languageId) {
                return RefundPolicy::with(['translates' => function($query) use ($languageId) {
                    $query->where('language_id', $languageId);
                }])->first();
            });

            if (!$refundPolicy) {
                return response()->json([
                    'success' => false,
                    'message' => 'Refund policy not found'
                ], 404);
            }

            $translation = $refundPolicy->translates->first();

            $refundPolicyData = [
                'id' => $refundPolicy->id,
                'content' => $translation ? $translation->content : ''
            ];

            return response()->json([
                'success' => true,
                'message' => 'Refund policy retrieved successfully',
                'data' => $refundPolicyData
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving refund policy: ' . $e->getMessage()
            ], 500);
        }
    }
} 