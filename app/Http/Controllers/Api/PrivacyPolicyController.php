<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PrivacyPolicy;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PrivacyPolicyController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/privacy-policy",
     *     summary="Get privacy policy content",
     *     description="Returns privacy policy content with translated content based on header language",
     *     tags={"Privacy Policy"},
     *     @OA\Parameter(
     *         name="locale",
     *         in="header",
     *         description="Language ID (1=Az, 2=En, 3=Ru)",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Privacy policy retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Privacy policy retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="content", type="string", example="<p>Gizlilik siyasəti məzmunu...</p>"),
     *                 @OA\Property(property="language_id", type="integer", example=1),
     *                 @OA\Property(property="language_code", type="string", example="az")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Privacy policy not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Privacy policy not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Error retrieving privacy policy")
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

            // Get privacy policy with cache
            $privacyPolicy = Cache::rememberForever("privacy_policy_{$languageId}", function () use ($languageId) {
                return PrivacyPolicy::with(['translates' => function($query) use ($languageId) {
                    $query->where('language_id', $languageId);
                }])->first();
            });

            if (!$privacyPolicy) {
                return response()->json([
                    'success' => false,
                    'message' => 'Privacy policy not found'
                ], 404);
            }

            $translation = $privacyPolicy->translates->first();

            $privacyPolicyData = [
                'id' => $privacyPolicy->id,
                'content' => $translation ? $translation->content : '',
                'language_id' => $languageId,
                'language_code' => $language->lang_code
            ];

            return response()->json([
                'success' => true,
                'message' => 'Privacy policy retrieved successfully',
                'data' => $privacyPolicyData
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving privacy policy: ' . $e->getMessage()
            ], 500);
        }
    }
} 