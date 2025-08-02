<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Dictionary;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use App\Traits\LocaleFromHeader;
use OpenApi\Annotations as OA;

class DictionaryController extends Controller
{
    use LocaleFromHeader;

    /**
     * @OA\Get(
     *     path="/api/dictionaries",
     *     operationId="getDictionaries",
     *     tags={"Dictionaries"},
     *     summary="Get all dictionary items",
     *     description="Returns a list of all dictionary items with their translations based on language ID sent via 'locale' header",
     *     @OA\Parameter(
     *         name="locale",
     *         in="header",
     *         description="Language_id from languages api",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *             example=1
     *         )
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
     *                     @OA\Property(property="keyword", type="string", example="welcome_message"),
     *                     @OA\Property(property="value", type="string", example="Xoş gəlmisiniz!")
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
     *             @OA\Property(property="message", type="string", example="Error retrieving dictionaries")
     *         )
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $languageId = $this->getLanguageIdFromHeader($request);
            $cacheKey = "api:dictionaries:all:lang:{$languageId}";

            $dictionaries = Cache::rememberForever($cacheKey, function () use ($languageId) {
                return Dictionary::with(['translates' => fn($q) => $q->where('language_id', $languageId)])
                    ->get()
                    ->map(function ($dictionary) {
                        $translate = $dictionary->translates->first();
                        return [
                            'id' => $dictionary->id,
                            'keyword' => $dictionary->keyword,
                            'value' => $translate->value ?? $dictionary->keyword
                        ];
                    });
            });

            return response()->json([
                'success' => true,
                'data' => $dictionaries
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving dictionaries: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/dictionaries/{keyword}",
     *     operationId="getDictionaryByKeyword",
     *     tags={"Dictionaries"},
     *     summary="Get dictionary item by keyword",
     *     description="Returns a dictionary item by its keyword with translation based on language ID sent via 'locale' header",
     *     @OA\Parameter(
     *         name="locale",
     *         in="header",
     *         description="Language_id from languages api",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *             example=1
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="keyword",
     *         in="path",
     *         required=true,
     *         description="Dictionary keyword",
     *         @OA\Schema(type="string", example="welcome_message")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="keyword", type="string", example="welcome_message"),
     *                 @OA\Property(property="value", type="string", example="Xoş gəlmisiniz!")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Dictionary item not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Dictionary item not found"),
     *             @OA\Property(property="data", type="object", nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Error retrieving dictionary item")
     *         )
     *     )
     * )
     */
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

            $translate = $dictionary->translates->first();

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $dictionary->id,
                    'keyword' => $dictionary->keyword,
                    'value' => $translate->value ?? $dictionary->keyword
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving dictionary item: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/dictionaries/by-keywords",
     *     operationId="getDictionariesByKeywordsArray",
     *     tags={"Dictionaries"},
     *     summary="Get multiple dictionary items by keywords array",
     *     description="Returns multiple dictionary items by array of keywords with translations based on language ID sent via 'locale' header",
     *     @OA\Parameter(
     *         name="locale",
     *         in="header",
     *         description="Language_id from languages api",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *             example=1
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="keywords",
     *                 type="array",
     *                 @OA\Items(type="string"),
     *                 example={"welcome_message", "about_title", "contact_info"}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="welcome_message", type="string", example="Xoş gəlmisiniz!"),
     *                 @OA\Property(property="about_title", type="string", example="Haqqımızda"),
     *                 @OA\Property(property="contact_info", type="string", example="Əlaqə məlumatları")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Keywords array is required")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Error retrieving dictionaries")
     *         )
     *     )
     * )
     */
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
                        $dictionary->keyword => $translate->value ?? $dictionary->keyword
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
