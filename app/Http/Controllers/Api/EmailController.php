<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Email;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use App\Traits\LocaleFromHeader;
use OpenApi\Annotations as OA;

class EmailController extends Controller
{
    use LocaleFromHeader;

    /**
     * @OA\Get(
     *     path="/api/emails",
     *     operationId="getEmails",
     *     tags={"Emails"},
     *     summary="Get all emails",
     *     description="Returns a list of all active emails, ordered by 'order' field",
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
     *                     @OA\Property(property="email", type="string", example="info@example.com")
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
     *             @OA\Property(property="message", type="string", example="Error retrieving emails")
     *         )
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $cacheKey = 'api_emails';
            
            $emails = Cache::rememberForever($cacheKey, function () {
                return Email::select(['id', 'email'])
                    ->where('status', true)
                    ->orderBy('order')
                    ->get();
            });

            return response()->json([
                'success' => true,
                'data' => $emails
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving emails: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }
} 