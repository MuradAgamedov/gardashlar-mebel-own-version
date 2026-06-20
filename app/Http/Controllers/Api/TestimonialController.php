<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Interfaces\Services\Front\TestimonialServiceInterface;
use App\Http\Resources\Api\TestimonialResource;
use App\Traits\LocaleFromHeader;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TestimonialController extends Controller
{
    use LocaleFromHeader;

    protected TestimonialServiceInterface $testimonialService;

    public function __construct(TestimonialServiceInterface $testimonialService)
    {
        $this->testimonialService = $testimonialService;
    }

    /**
     * Display a listing of active testimonials.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $languageId = $this->getLanguageIdFromHeader($request);

            $testimonials = $this->testimonialService->getActiveOrderedWithTranslations($languageId);

            return response()->json([
                'success' => true,
                'data' => TestimonialResource::collection($testimonials),
                'message' => 'Testimonials retrieved successfully',
                'total' => $testimonials->count(),
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
            $languageId = $this->getLanguageIdFromHeader($request);
            
            $testimonial = $this->testimonialService->first(['translates' => fn($q) => $q->where('language_id', $languageId)], [
                'id' => $id,
                'status' => true
            ]);

            if (!$testimonial) {
                return response()->json([
                    'success' => false,
                    'message' => 'Testimonial not found',
                    'data' => null
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => new TestimonialResource($testimonial),
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
            $testimonials = $this->testimonialService->getActiveOrdered(['translates.language']);

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
                    'customer_image_url' => $testimonial->customer_image ? asset('storage/' . $testimonial->customer_image) : asset('images/default_image.webp'),
                    'company_logo_url' => $testimonial->company_logo ? asset('storage/' . $testimonial->company_logo) : asset('images/default_image.webp'),
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