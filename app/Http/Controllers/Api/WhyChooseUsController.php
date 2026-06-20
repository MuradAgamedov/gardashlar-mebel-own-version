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

    /**
     * @OA\Get(
     *     path="/api/why-choose-us",
     *     operationId="getWhyChooseUs",
     *     tags={"Why Choose Us"},
     *     summary="Get Why Choose Us data with sliders",
     *     description="Returns Why Choose Us content and active sliders",
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
     *                 type="object",
     *                 @OA\Property(
     *                     property="why_choose_us",
     *                     type="object",
     *                     description="Why Choose Us content",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="first_icon_url", type="string", example="https://example.com/icon1.png"),
     *                     @OA\Property(property="second_icon_url", type="string", example="https://example.com/icon2.png"),
     *                     @OA\Property(property="third_icon_url", type="string", example="https://example.com/icon3.png"),
     *                     @OA\Property(property="fourth_icon_url", type="string", example="https://example.com/icon4.png"),
     *                     @OA\Property(property="first_title", type="string", nullable=true, example="Quality Service"),
     *                     @OA\Property(property="first_description", type="string", nullable=true, example="We provide high quality services"),
     *                     @OA\Property(property="second_title", type="string", nullable=true, example="Fast Delivery"),
     *                     @OA\Property(property="second_description", type="string", nullable=true, example="Quick and reliable delivery"),
     *                     @OA\Property(property="third_title", type="string", nullable=true, example="24/7 Support"),
     *                     @OA\Property(property="third_description", type="string", nullable=true, example="Round the clock customer support"),
     *                     @OA\Property(property="fourth_title", type="string", nullable=true, example="Best Prices"),
     *                     @OA\Property(property="fourth_description", type="string", nullable=true, example="Competitive pricing for all services")
     *                 ),
     *                 @OA\Property(
     *                     property="sliders",
     *                     type="array",
     *                     description="Active sliders",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="image_url", type="string", example="https://example.com/slider1.jpg"),
     *                         @OA\Property(property="video_id", type="string", nullable=true, example="tzQmYPOv4A0", description="YouTube video ID extracted from video_link")
     *                     )
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
     *             @OA\Property(property="message", type="string", example="Error retrieving Why Choose Us data")
     *         )
     *     )
     * )
     */
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
                return response()->json([
                    'success' => false,
                    'message' => 'Why Choose Us data not found',
                    'data' => null
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'why_choose_us' => new WhyChooseUsResource($data['why_choose_us']),
                    'sliders' => WhyChooseUsSliderResource::collection($data['sliders'])
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving Why Choose Us data: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }
} 