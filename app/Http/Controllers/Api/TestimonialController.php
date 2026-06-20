<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Interfaces\Services\Front\TestimonialServiceInterface;
use App\Http\Resources\Api\TestimonialResource;
use App\Http\Resources\Api\TestimonialWithTranslationsResource;
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

    public function index(Request $request): JsonResponse
    {
        try {
            $languageId = $this->getLanguageIdFromHeader($request);

            $testimonials = $this->testimonialService->getActiveOrderedWithTranslations($languageId);

            return $this->success(TestimonialResource::collection($testimonials), 'Testimonials retrieved successfully');

        } catch (\Exception $e) {
            return $this->serverError('Error retrieving testimonials: ' . $e->getMessage());
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
                return $this->notFound('Testimonial not found');
            }

            return $this->success(new TestimonialResource($testimonial), 'Testimonial retrieved successfully');

        } catch (\Exception $e) {
            return $this->serverError('Error retrieving testimonial: ' . $e->getMessage());
        }
    }

    /**
     * Get testimonials with all translations.
     */
    public function withTranslations(Request $request): JsonResponse
    {
        try {
            $testimonials = $this->testimonialService->getActiveOrdered(['translates.language']);

            return $this->success(TestimonialWithTranslationsResource::collection($testimonials), 'Testimonials with translations retrieved successfully');

        } catch (\Exception $e) {
            return $this->serverError('Error retrieving testimonials: ' . $e->getMessage());
        }
    }
} 