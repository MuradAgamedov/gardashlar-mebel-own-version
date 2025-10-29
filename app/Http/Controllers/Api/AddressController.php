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

    
    public function index(Request $request): JsonResponse
    {
        try {
            // Get language ID from header using trait
            $languageId = $this->getLanguageIdFromHeader($request);
            
            $cacheKey = "api_addresses_{$languageId}";
            
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
} 