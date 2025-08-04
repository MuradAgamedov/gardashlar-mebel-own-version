<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Phone;
use App\Models\PhoneType;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use App\Traits\LocaleFromHeader;
use OpenApi\Annotations as OA;

class PhoneController extends Controller
{
    use LocaleFromHeader;

    /**
     * @OA\Get(
     *     path="/api/general-phones",
     *     operationId="getGeneralPhones",
     *     tags={"Phones"},
     *     summary="Get general phones",
     *     description="Returns a list of phones with phone_type_id = 3, ordered by 'order' field",
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
     *                     @OA\Property(property="phone", type="string", example="+994501234567"),
     *                     @OA\Property(property="phone_type_id", type="integer", example=3),
     *                     @OA\Property(property="order", type="integer", example=1),
     *                     @OA\Property(property="status", type="boolean", example=true),
     *                     @OA\Property(
     *                         property="phone_type",
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=3),
     *                         @OA\Property(property="order", type="integer", example=1),
     *                         @OA\Property(property="status", type="boolean", example=true),
     *                         @OA\Property(
     *                             property="translates",
     *                             type="array",
     *                             @OA\Items(
     *                                 type="object",
     *                                 @OA\Property(property="id", type="integer", example=1),
     *                                 @OA\Property(property="title", type="string", example="General"),
     *                                 @OA\Property(property="language_id", type="integer", example=1),
     *                                 @OA\Property(
     *                                     property="language",
     *                                     type="object",
     *                                     @OA\Property(property="id", type="integer", example=1),
     *                                     @OA\Property(property="title", type="string", example="Azərbaycan dili"),
     *                                     @OA\Property(property="lang_code", type="string", example="az"),
     *                                     @OA\Property(property="lang_name", type="string", example="Azerbaijani")
     *                                 )
     *                             )
     *                         )
     *                     )
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
     *             @OA\Property(property="message", type="string", example="Error retrieving general phones")
     *         )
     *     )
     * )
     */
    public function generalPhones(Request $request): JsonResponse
    {
        try {
            $cacheKey = 'api_general_phones';
            
            $phones = Cache::rememberForever($cacheKey, function () {
                return Phone::with(['phoneType.translates.language'])
                    ->where('phone_type_id', 3)
                    ->where('status', true)
                    ->orderBy('order')
                    ->get();
            });

            return response()->json([
                'success' => true,
                'data' => $phones
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving general phones: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/phones",
     *     operationId="getAllPhones",
     *     tags={"Phones"},
     *     summary="Get all phones",
     *     description="Returns a list of all active phones with their phone types, ordered by 'order' field",
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
     *                     @OA\Property(property="phone", type="string", example="+994501234567"),
     *                     @OA\Property(property="phone_type_id", type="integer", example=1),
     *                     @OA\Property(property="order", type="integer", example=1),
     *                     @OA\Property(property="status", type="boolean", example=true),
     *                     @OA\Property(
     *                         property="phone_type",
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="order", type="integer", example=1),
     *                         @OA\Property(property="status", type="boolean", example=true),
     *                         @OA\Property(
     *                             property="translates",
     *                             type="array",
     *                             @OA\Items(
     *                                 type="object",
     *                                 @OA\Property(property="id", type="integer", example=1),
     *                                 @OA\Property(property="title", type="string", example="Support"),
     *                                 @OA\Property(property="language_id", type="integer", example=1),
     *                                 @OA\Property(
     *                                     property="language",
     *                                     type="object",
     *                                     @OA\Property(property="id", type="integer", example=1),
     *                                     @OA\Property(property="title", type="string", example="Azərbaycan dili"),
     *                                     @OA\Property(property="lang_code", type="string", example="az"),
     *                                     @OA\Property(property="lang_name", type="string", example="Azerbaijani")
     *                                 )
     *                             )
     *                         )
     *                     )
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
     *             @OA\Property(property="message", type="string", example="Error retrieving phones")
     *         )
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $cacheKey = 'api_all_phones';
            
            $phones = Cache::rememberForever($cacheKey, function () {
                return Phone::with(['phoneType.translates.language'])
                    ->where('status', true)
                    ->orderBy('order')
                    ->get();
            });

            return response()->json([
                'success' => true,
                'data' => $phones
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving phones: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/phones/{phoneTypeId}",
     *     operationId="getPhonesByType",
     *     tags={"Phones"},
     *     summary="Get phones by type",
     *     description="Returns a list of phones for a specific phone type, ordered by 'order' field",
     *     @OA\Parameter(
     *         name="phoneTypeId",
     *         in="path",
     *         required=true,
     *         description="Phone type ID",
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
     *                     @OA\Property(property="phone", type="string", example="+994501234567"),
     *                     @OA\Property(property="phone_type_id", type="integer", example=1),
     *                     @OA\Property(property="order", type="integer", example=1),
     *                     @OA\Property(property="status", type="boolean", example=true),
     *                     @OA\Property(
     *                         property="phone_type",
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="order", type="integer", example=1),
     *                         @OA\Property(property="status", type="boolean", example=true),
     *                         @OA\Property(
     *                             property="translates",
     *                             type="array",
     *                             @OA\Items(
     *                                 type="object",
     *                                 @OA\Property(property="id", type="integer", example=1),
     *                                 @OA\Property(property="title", type="string", example="Support"),
     *                                 @OA\Property(property="language_id", type="integer", example=1),
     *                                 @OA\Property(
     *                                     property="language",
     *                                     type="object",
     *                                     @OA\Property(property="id", type="integer", example=1),
     *                                     @OA\Property(property="title", type="string", example="Azərbaycan dili"),
     *                                     @OA\Property(property="lang_code", type="string", example="az"),
     *                                     @OA\Property(property="lang_name", type="string", example="Azerbaijani")
     *                                 )
     *                             )
     *                         )
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Phone type not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Phone type not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Error retrieving phones")
     *         )
     *     )
     * )
     */
    public function getByType(Request $request, $phoneTypeId): JsonResponse
    {
        try {
            // Check if phone type exists
            $phoneType = PhoneType::find($phoneTypeId);
            if (!$phoneType) {
                return response()->json([
                    'success' => false,
                    'message' => 'Phone type not found',
                    'data' => []
                ], 404);
            }

            $cacheKey = "api_phones_type_{$phoneTypeId}";
            
            $phones = Cache::rememberForever($cacheKey, function () use ($phoneTypeId) {
                return Phone::with(['phoneType.translates.language'])
                    ->where('phone_type_id', $phoneTypeId)
                    ->where('status', true)
                    ->orderBy('order')
                    ->get();
            });

            return response()->json([
                'success' => true,
                'data' => $phones
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving phones: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }
} 