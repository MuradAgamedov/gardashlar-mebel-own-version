<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProductType;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use App\Traits\LocaleFromHeader;
use OpenApi\Annotations as OA;

class ProductTypeController extends Controller
{
    use LocaleFromHeader;

    public function index(Request $request): JsonResponse
    {
        try {
            $languageId = $this->getLanguageIdFromHeader($request);
            $cacheKey = "api:product-types:lang:{$languageId}";
            
            $productTypes = Cache::rememberForever($cacheKey, function () use ($languageId) {
                return ProductType::where('status', true)
                    ->orderBy('order')
                    ->get()
                    ->map(function ($type) use ($languageId) {
                        $translation = $type->translate($languageId);
                        return [
                            'id' => $type->id,
                            'title' => $translation ? $translation->title : 'No title'
                        ];
                    });
            });

            return response()->json([
                'success' => true,
                'data' => $productTypes
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving product types: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }
} 