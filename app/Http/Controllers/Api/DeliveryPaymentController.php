<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DeliveryPayment;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class DeliveryPaymentController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/delivery-payment",
     *     summary="Get delivery payment content",
     *     description="Returns delivery payment content with translated content based on header language",
     *     tags={"Delivery Payment"},
     *     @OA\Parameter(
     *         name="locale",
     *         in="header",
     *         description="Language ID (1=Az, 2=En, 3=Ru)",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Delivery payment retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Delivery payment retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="content", type="string", example="<p>Çatdırılma və ödəniş məzmunu...</p>")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Delivery payment not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Delivery payment not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Error retrieving delivery payment")
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

            // Get delivery payment with cache
            $deliveryPayment = Cache::rememberForever("delivery_payment_{$languageId}", function () use ($languageId) {
                return DeliveryPayment::with(['translates' => function($query) use ($languageId) {
                    $query->where('language_id', $languageId);
                }])->first();
            });

            if (!$deliveryPayment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Delivery payment not found'
                ], 404);
            }

            $translation = $deliveryPayment->translates->first();

            $deliveryPaymentData = [
                'id' => $deliveryPayment->id,
                'content' => $translation ? $translation->content : ''
            ];

            return response()->json([
                'success' => true,
                'message' => 'Delivery payment retrieved successfully',
                'data' => $deliveryPaymentData
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving delivery payment: ' . $e->getMessage()
            ], 500);
        }
    }
} 