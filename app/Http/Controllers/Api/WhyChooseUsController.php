<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Interfaces\Services\Front\WhyChooseUsServiceInterface;
use App\Interfaces\Services\Front\WhyChooseUsSliderServiceInterface;
use App\Http\Resources\Api\WhyChooseUsResource;
use App\Http\Resources\Api\WhyChooseUsSliderResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use App\Traits\LocaleFromHeader;
use OpenApi\Annotations as OA;

class WhyChooseUsController extends Controller
{
    use LocaleFromHeader;

    protected WhyChooseUsServiceInterface $whyChooseUsService;
    protected WhyChooseUsSliderServiceInterface $whyChooseUsSliderService;

    public function __construct(
        WhyChooseUsServiceInterface $whyChooseUsService,
        WhyChooseUsSliderServiceInterface $whyChooseUsSliderService
    ) {
        $this->whyChooseUsService = $whyChooseUsService;
        $this->whyChooseUsSliderService = $whyChooseUsSliderService;
    }

   
    public function index(Request $request): JsonResponse
    {
        try {
            $languageId = $this->getLanguageIdFromHeader($request);
            $cacheKey = "api:why-choose-us:lang:{$languageId}";
            
            $data = Cache::rememberForever($cacheKey, function () use ($languageId) {
                $whyChooseUs = $this->whyChooseUsService->first(['translates' => function($q) use ($languageId) {
                    $q->where('language_id', $languageId);
                }]);

                if (!$whyChooseUs) {
                    return null;
                }

                $sliders = $this->whyChooseUsSliderService->getActiveOrderedWithTranslations($languageId);

                return [
                    'why_choose_us' => $whyChooseUs,
                    'sliders' => $sliders
                ];
            });

            if (!$data) {
                return $this->notFound('Why Choose Us data not found');
            }

            return $this->success([
                'why_choose_us' => new WhyChooseUsResource($data['why_choose_us']),
                'sliders' => WhyChooseUsSliderResource::collection($data['sliders'])
            ]);

        } catch (\Exception $e) {
            return $this->serverError('Error retrieving Why Choose Us data: ' . $e->getMessage());
        }
    }
} 