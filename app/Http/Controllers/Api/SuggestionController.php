<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Suggestion;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

/**
 * @OA\Tag(
 *     name="Suggestions",
 *     description="API Endpoints for suggestions"
 * )
 */
class SuggestionController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/suggestions",
     *     summary="Get all active suggestions",
     *     tags={"Suggestions"},
     *     @OA\Parameter(
     *         name="locale",
     *         in="header",
     *         description="Language ID (1=az, 2=en, 3=ru)",
     *         required=false,
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Suggestions retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="title", type="string", example="Məhsul Tövsiyyəsi 1"),
     *                     @OA\Property(property="image_alt_text", type="string", example="Məhsul tövsiyyəsi şəkli"),
     *                     @OA\Property(property="image", type="string", example="https://admin.gardaslar.coder.az/storage/suggestions/sample-suggestion-1.jpg"),
     *                     @OA\Property(property="pdf", type="string", nullable=true, example="https://admin.gardaslar.coder.az/storage/suggestions/pdfs/sample-pdf-1.pdf"),
     *                     @OA\Property(property="has_pdf", type="boolean", example=true),
     *                     @OA\Property(property="order", type="integer", example=1)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid language ID",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Invalid language ID")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Error retrieving suggestions")
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        try {
            $languageId = $request->header('locale', 1);
            $language = Language::find($languageId);
            
            if (!$language) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid language ID'
                ], 400);
            }

            $suggestions = Cache::rememberForever("suggestions_{$languageId}", function () use ($languageId) {
                return Suggestion::with(['translates' => function($query) use ($languageId) {
                    $query->where('language_id', $languageId);
                }])
                ->where('status', true)
                ->orderBy('order')
                ->get();
            });

            $suggestionsData = $suggestions->map(function ($suggestion) {
                $translation = $suggestion->translates->first();
                return [
                    'id' => $suggestion->id,
                    'title' => $translation ? $translation->title : '',
                    'image_alt_text' => $translation ? $translation->image_alt_text : '',
                    'image' => $suggestion->image ? asset('storage/' . $suggestion->image) : asset('images/default_image.webp'),
                    'pdf' => $suggestion->pdf ? asset('storage/' . $suggestion->pdf) : null,
                    'has_pdf' => !empty($suggestion->pdf),
                    'order' => $suggestion->order
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Suggestions retrieved successfully',
                'data' => $suggestionsData
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving suggestions: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/suggestions/{id}",
     *     summary="Get a specific suggestion by ID",
     *     tags={"Suggestions"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Suggestion ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="locale",
     *         in="header",
     *         description="Language ID (1=az, 2=en, 3=ru)",
     *         required=false,
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Suggestion retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="title", type="string", example="Məhsul Tövsiyyəsi 1"),
     *                 @OA\Property(property="image_alt_text", type="string", example="Məhsul tövsiyyəsi şəkli"),
     *                 @OA\Property(property="image", type="string", example="suggestions/sample-suggestion-1.jpg"),
     *                 @OA\Property(property="pdf", type="string", nullable=true, example="suggestions/pdfs/sample-pdf-1.pdf"),
     *                 @OA\Property(property="order", type="integer", example=1)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Suggestion not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Suggestion not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Error retrieving suggestion")
     *         )
     *     )
     * )
     */
    public function show(Request $request, $id)
    {
        try {
            $languageId = $request->header('locale', 1);
            $language = Language::find($languageId);
            
            if (!$language) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid language ID'
                ], 400);
            }

            $suggestion = Cache::rememberForever("suggestion_{$id}_{$languageId}", function () use ($id, $languageId) {
                return Suggestion::with(['translates' => function($query) use ($languageId) {
                    $query->where('language_id', $languageId);
                }])
                ->where('status', true)
                ->find($id);
            });

            if (!$suggestion) {
                return response()->json([
                    'success' => false,
                    'message' => 'Suggestion not found'
                ], 404);
            }

            $translation = $suggestion->translates->first();
            $suggestionData = [
                'id' => $suggestion->id,
                'title' => $translation ? $translation->title : '',
                'image_alt_text' => $translation ? $translation->image_alt_text : '',
                'image' => $suggestion->image ? asset('storage/' . $suggestion->image) : asset('images/default_image.webp'),
                'pdf' => $suggestion->pdf ? asset('storage/' . $suggestion->pdf) : null,
                'has_pdf' => !empty($suggestion->pdf),
                'order' => $suggestion->order
            ];

            return response()->json([
                'success' => true,
                'message' => 'Suggestion retrieved successfully',
                'data' => $suggestionData
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving suggestion: ' . $e->getMessage()
            ], 500);
        }
    }
} 