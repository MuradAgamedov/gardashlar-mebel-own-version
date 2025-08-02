<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TestimonialController extends Controller
{
    /**
     * Display a listing of active testimonials.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Testimonial::with(['translates.language'])
                ->where('status', true)
                ->orderBy('order');

            // Filter by language if provided
            if ($request->has('lang')) {
                $langCode = $request->get('lang');
                $query->whereHas('translates.language', function ($q) use ($langCode) {
                    $q->where('lang_code', $langCode);
                });
            }

            $testimonials = $query->get();

            $formattedTestimonials = $testimonials->map(function ($testimonial) use ($request) {
                $langCode = $request->get('lang', 'az'); // Default to Azerbaijani
                
                // Get translation for the requested language
                $translation = $testimonial->translates->firstWhere('language.lang_code', $langCode);
                
                // If no translation found for requested language, get the first available
                if (!$translation) {
                    $translation = $testimonial->translates->first();
                }

                return [
                    'id' => $testimonial->id,
                    'customer_image_url' => $testimonial->customer_image ? asset('storage/' . $testimonial->customer_image) : null,
                    'company_logo_url' => $testimonial->company_logo ? asset('storage/' . $testimonial->company_logo) : null,
                    'full_name' => $translation ? $translation->full_name : null,
                    'position' => $translation ? $translation->position : null,
                    'comment' => $translation ? $translation->comment : null,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $formattedTestimonials,
                'message' => 'Testimonials retrieved successfully',
                'total' => $formattedTestimonials->count(),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving testimonials: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }

    /**
     * Display the specified testimonial.
     */
    public function show(Request $request, $id): JsonResponse
    {
        try {
            $testimonial = Testimonial::with(['translates.language'])
                ->where('status', true)
                ->findOrFail($id);

            $langCode = $request->get('lang', 'az'); // Default to Azerbaijani
            
            // Get translation for the requested language
            $translation = $testimonial->translates->firstWhere('language.lang_code', $langCode);
            
            // If no translation found for requested language, get the first available
            if (!$translation) {
                $translation = $testimonial->translates->first();
            }

            $formattedTestimonial = [
                'id' => $testimonial->id,
                'customer_image_url' => $testimonial->customer_image ? asset('storage/' . $testimonial->customer_image) : null,
                'company_logo_url' => $testimonial->company_logo ? asset('storage/' . $testimonial->company_logo) : null,
                'full_name' => $translation ? $translation->full_name : null,
                'position' => $translation ? $translation->position : null,
                'comment' => $translation ? $translation->comment : null,
            ];

            return response()->json([
                'success' => true,
                'data' => $formattedTestimonial,
                'message' => 'Testimonial retrieved successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving testimonial: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    /**
     * Get testimonials with all translations.
     */
    public function withTranslations(Request $request): JsonResponse
    {
        try {
            $query = Testimonial::with(['translates.language'])
                ->where('status', true)
                ->orderBy('order');

            $testimonials = $query->get();

            $formattedTestimonials = $testimonials->map(function ($testimonial) {
                $translations = $testimonial->translates->map(function ($translation) {
                    return [
                        'language_code' => $translation->language->lang_code,
                        'language_name' => $translation->language->title,
                        'full_name' => $translation->full_name,
                        'position' => $translation->position,
                        'comment' => $translation->comment,
                    ];
                });

                return [
                    'id' => $testimonial->id,
                    'customer_image_url' => $testimonial->customer_image ? asset('storage/' . $testimonial->customer_image) : null,
                    'company_logo_url' => $testimonial->company_logo ? asset('storage/' . $testimonial->company_logo) : null,
                    'translations' => $translations,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $formattedTestimonials,
                'message' => 'Testimonials with translations retrieved successfully',
                'total' => $formattedTestimonials->count(),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving testimonials: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }
} 