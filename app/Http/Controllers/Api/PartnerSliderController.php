<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\PartnerSliderResource;
use App\Interfaces\Services\Front\PartnerSliderServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Traits\LocaleFromHeader;

class PartnerSliderController extends Controller
{
    use LocaleFromHeader;

    protected PartnerSliderServiceInterface $partnerSliderService;

    public function __construct(PartnerSliderServiceInterface $partnerSliderService)
    {
        $this->partnerSliderService = $partnerSliderService;
    }

    /**
     * Get all active partner sliders with translations
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $languageId = $this->getLanguageIdFromHeader($request);

            $sliders = $this->partnerSliderService->getActiveOrderedWithTranslations($languageId);

            return $this->success(PartnerSliderResource::collection($sliders), 'Partner sliders retrieved successfully');

        } catch (\Exception $e) {
            return $this->serverError('Error retrieving partner sliders: ' . $e->getMessage());
        }
    }

    /**
     * Get a specific partner slider by ID
     */
    public function show(Request $request, $id): JsonResponse
    {
        try {
            $languageId = $this->getLanguageIdFromHeader($request);

            $slider = $this->partnerSliderService->getByIdWithTranslations((int)$id, $languageId);

            if (!$slider) {
                return $this->notFound('Partner slider not found');
            }

            return $this->success(new PartnerSliderResource($slider), 'Partner slider retrieved successfully');

        } catch (\Exception $e) {
            return $this->serverError('Error retrieving partner slider: ' . $e->getMessage());
        }
    }

    /**
     * Get partner sliders with all language translations
     */
    public function withAllTranslations(): JsonResponse
    {
        try {
            $sliders = $this->partnerSliderService->getActiveOrdered(['translates.language'])
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

            return $this->success($sliders, 'Partner sliders with all translations retrieved successfully');

        } catch (\Exception $e) {
            return $this->serverError('Error retrieving partner sliders: ' . $e->getMessage());
        }
    }
}
