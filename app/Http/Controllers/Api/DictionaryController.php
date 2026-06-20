<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\DictionaryResource;
use App\Models\Dictionary;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use App\Traits\LocaleFromHeader;
use OpenApi\Annotations as OA;

class DictionaryController extends Controller
{
    use LocaleFromHeader;

    
    public function index(Request $request): JsonResponse
    {
        try {
            $languageId = $this->getLanguageIdFromHeader($request);
            $cacheKey = "api:dictionaries:all:lang:{$languageId}";

            $dictionaries = Cache::rememberForever($cacheKey, function () use ($languageId) {
                return Dictionary::with(['translates' => fn($q) => $q->where('language_id', $languageId)])
                    ->get();
            });

            return response()->json([
                'success' => true,
                'data' => DictionaryResource::collection($dictionaries)
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving dictionaries: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }

   
    public function show(Request $request, string $keyword): JsonResponse
    {
        try {
            $languageId = $this->getLanguageIdFromHeader($request);

            $dictionary = Dictionary::where('keyword', $keyword)
                ->with(['translates' => fn($q) => $q->where('language_id', $languageId)])
                ->first();

            if (!$dictionary) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dictionary item not found',
                    'data' => null
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => new DictionaryResource($dictionary)
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving dictionary item: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

  
    public function getByKeywordsArray(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'keywords' => 'required|array',
                'keywords.*' => 'string'
            ]);

            $languageId = $this->getLanguageIdFromHeader($request);
            $keywords = $request->input('keywords');

            $dictionaries = Dictionary::whereIn('keyword', $keywords)
                ->with(['translates' => fn($q) => $q->where('language_id', $languageId)])
                ->get()
                ->mapWithKeys(function ($dictionary) {
                    $translate = $dictionary->translates->first();
                    return [
                        $dictionary->keyword => $translate?->value ?? $dictionary->keyword
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $dictionaries
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error: ' . $e->getMessage(),
                'data' => []
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving dictionaries: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }
}
