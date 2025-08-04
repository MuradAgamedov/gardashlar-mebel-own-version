<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use App\Traits\LocaleFromHeader;
use OpenApi\Annotations as OA;

class AddressController extends Controller
{
    use LocaleFromHeader;

    /**
     * @OA\Get(
     *     path="/api/addresses",
     *     operationId="getAddresses",
     *     tags={"Addresses"},
     *     summary="Get all addresses",
     *     description="Returns a list of all active addresses with their types, translated based on Accept-Language header",
     *     @OA\Parameter(
     *         name="Accept-Language",
     *         in="header",
     *         description="Language code (e.g., az, en, ru)",
     *         required=false,
     *         @OA\Schema(type="string", example="az")
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
     *                     @OA\Property(property="type", type="string", example="Baş ofis"),
     *                     @OA\Property(property="address", type="string", example="Bakı şəhəri, Nərimanov rayonu, Atatürk prospekti 123")
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
     *             @OA\Property(property="message", type="string", example="Error retrieving addresses")
     *         )
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        try {
            // Get language from header
            $language = $this->getLanguageFromHeader($request);
            $languageId = $language ? $language->id : 1; // Default to Azerbaijani
            
            $cacheKey = "api_addresses_{$language->lang_code}";
            
            $addresses = Cache::rememberForever($cacheKey, function () use ($languageId) {
                return Address::with(['addressType.translates' => function($query) use ($languageId) {
                    $query->where('language_id', $languageId);
                }, 'translates' => function($query) use ($languageId) {
                    $query->where('language_id', $languageId);
                }])
                ->where('status', true)
                ->orderBy('order')
                ->get()
                ->map(function ($address) {
                    $typeTranslation = $address->addressType->translates->first();
                    $addressTranslation = $address->translates->first();
                    
                    return [
                        'id' => $address->id,
                        'type' => $typeTranslation ? $typeTranslation->title : '',
                        'address' => $addressTranslation ? $addressTranslation->address : ''
                    ];
                });
            });

            return response()->json([
                'success' => true,
                'data' => $addresses
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving addresses: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }

    /**
     * Get language from Accept-Language header
     */
    private function getLanguageFromHeader(Request $request)
    {
        $acceptLanguage = $request->header('Accept-Language', 'az');
        $langCode = substr($acceptLanguage, 0, 2);
        
        return Language::where('lang_code', $langCode)
            ->where('status', true)
            ->first() ?? Language::where('is_default', true)->first();
    }
} 