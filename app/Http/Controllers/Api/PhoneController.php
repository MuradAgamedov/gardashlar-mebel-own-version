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

   
    public function phones(Request $request): JsonResponse
    {
        try {
            $languageId = $this->getLanguageIdFromHeader($request);
            $cacheKey = "api_phones_{$languageId}";
            
            $phoneTypes = Cache::rememberForever($cacheKey, function () use ($languageId) {
                return \App\Models\PhoneType::with([
                    'translates' => function($query) use ($languageId) {
                        $query->where('language_id', $languageId);
                    },
                    'phones' => function($query) {
                        $query->where('status', true)
                              ->where('id', '!=', 15) // 👈 15 id-li phone-u çıxartdıq
                              ->orderBy('order');
                    }
                ])
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

    public function specialPhone(Request $request): JsonResponse
{
    try {
        $cacheKey = 'api_special_phone_15';

        $phone = Cache::rememberForever($cacheKey, function () {
            return Phone::select(['id', 'phone'])
                ->where('id', 15)
                ->where('status', true)
                ->first(); // yalnız birini qaytarır
        });

        return response()->json([
            'success' => true,
            'data' => $phone
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error retrieving special phone: ' . $e->getMessage(),
            'data' => null
        ], 500);
    }
}

    
} 