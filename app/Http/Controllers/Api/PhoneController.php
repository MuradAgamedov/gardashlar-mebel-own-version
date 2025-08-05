<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Phone;
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
     *     description="Returns a list of phone numbers with phone_type_id = 3 (support phones), ordered by 'order' field",
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
     *                     @OA\Property(property="phone", type="string", example="+994501234567")
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
                return Phone::select(['id', 'phone'])
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
     *     operationId="getPhones",
     *     tags={"Phones"},
     *     summary="Get phones grouped by type",
     *     description="Returns phone types with their phones, excluding support phones (type_id != 3)",
     *     @OA\Parameter(
     *         name="locale",
     *         in="header",
     *         description="Language ID (1=En, 3=Az)",
     *         required=true,
     *         @OA\Schema(type="integer", example=3)
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
     *                     @OA\Property(property="type", type="string", example="Əsas"),
     *                     @OA\Property(
     *                         property="phones",
     *                         type="array",
     *                         @OA\Items(
     *                             type="object",
     *                             @OA\Property(property="id", type="integer", example=1),
     *                             @OA\Property(property="phone", type="string", example="+994501234567")
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
     *             @OA\Property(property="message", type="string", example="Error retrieving phones by type")
     *         )
     *     )
     * )
     */
         public function phones(Request $request): JsonResponse
    {
        try {
            $languageId = $this->getLanguageIdFromHeader($request);
                         $cacheKey = "api_phones_{$languageId}";
            
            $phoneTypes = Cache::rememberForever($cacheKey, function () use ($languageId) {
                return \App\Models\PhoneType::with(['translates' => function($query) use ($languageId) {
                    $query->where('language_id', $languageId);
                }, 'phones' => function($query) {
                    $query->where('status', true)->orderBy('order');
                }])
                ->where('id', '!=', 3)
                ->where('status', true)
                ->orderBy('order')
                ->get()
                ->map(function ($phoneType) {
                    $translation = $phoneType->translates->first();
                    return [
                        'id' => $phoneType->id,
                        'type' => $translation ? $translation->title : '',
                        'phones' => $phoneType->phones->map(function ($phone) {
                            return [
                                'id' => $phone->id,
                                'phone' => $phone->phone
                            ];
                        })
                    ];
                });
            });

            return response()->json([
                'success' => true,
                'data' => $phoneTypes
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