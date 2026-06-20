<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Suggestion;
use App\Models\Language;
use App\Traits\LocaleFromHeader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;


class SuggestionController extends Controller
{
    use LocaleFromHeader;
   
    public function index(Request $request)
    {
        try {
            $languageId = $this->getLanguageIdFromHeader($request);
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

 
    public function show(Request $request, $id)
    {
        try {
            $languageId = $this->getLanguageIdFromHeader($request);
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