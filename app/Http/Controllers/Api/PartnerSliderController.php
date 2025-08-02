<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PartnerSlider;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PartnerSliderController extends Controller
{
    /**
     * Get all active partner sliders with translations
     */
    public function index(Request $request): JsonResponse
    {
        try {
            // Get language from header or use default
            $languageCode = $request->header('Accept-Language', 'az');
            $language = Language::where('lang_code', $languageCode)->first();
            
            if (!$language) {
                $language = Language::where('is_default', true)->first();
            }

            $sliders = PartnerSlider::with(['translates' => function($query) use ($language) {
                $query->where('language_id', $language->id);
            }])
            ->active()
            ->ordered()
            ->get()
            ->map(function ($slider) {
                $translation = $slider->translates->first();
                
                return [
                    'id' => $slider->id,
                    'image_url' => $slider->image_url,
                    'title' => $translation ? $translation->title : null,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $sliders,
                'message' => 'Partner sliders retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving partner sliders: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get a specific partner slider by ID
     */
    public function show(Request $request, $id): JsonResponse
    {
        try {
            // Get language from header or use default
            $languageCode = $request->header('Accept-Language', 'az');
            $language = Language::where('lang_code', $languageCode)->first();
            
            if (!$language) {
                $language = Language::where('is_default', true)->first();
            }

            $slider = PartnerSlider::with(['translates' => function($query) use ($language) {
                $query->where('language_id', $language->id);
            }])
            ->active()
            ->find($id);

            if (!$slider) {
                return response()->json([
                    'success' => false,
                    'message' => 'Partner slider not found'
                ], 404);
            }

            $translation = $slider->translates->first();

            $data = [
                'id' => $slider->id,
                'image_url' => $slider->image_url,
                'title' => $translation ? $translation->title : null,
            ];

            return response()->json([
                'success' => true,
                'data' => $data,
                'message' => 'Partner slider retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving partner slider: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get partner sliders with all language translations
     */
    public function withAllTranslations(): JsonResponse
    {
        try {
            $sliders = PartnerSlider::with('translates.language')
                ->active()
                ->ordered()
                ->get()
                ->map(function ($slider) {
                    $translations = $slider->translates->map(function ($translation) {
                        return [
                            'language_id' => $translation->language_id,
                            'language_code' => $translation->language->lang_code,
                            'language_title' => $translation->language->title,
                            'title' => $translation->title,
                        ];
                    });

                    return [
                        'id' => $slider->id,
                        'image_url' => $slider->image_url,
                        'translations' => $translations,
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $sliders,
                'message' => 'Partner sliders with all translations retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving partner sliders: ' . $e->getMessage()
            ], 500);
        }
    }
}
