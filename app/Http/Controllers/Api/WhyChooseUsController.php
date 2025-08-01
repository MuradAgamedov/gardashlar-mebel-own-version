<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WhyChooseUs;
use App\Models\WhyChooseUsSlider;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use App\Traits\LocaleFromHeader;
use OpenApi\Annotations as OA;

class WhyChooseUsController extends Controller
{
    use LocaleFromHeader;

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
     *                     @OA\Property(property="content", type="string", example="Why choose us content...")
     *                 ),
     *                 @OA\Property(
     *                     property="sliders",
     *                     type="array",
     *                     description="Active sliders",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="order", type="integer", example=1),
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
                // Get Why Choose Us data
                $whyChooseUs = WhyChooseUs::getFirstOrCreate();
                $translation = $whyChooseUs->translate($languageId);
                
                // Get active sliders
                $sliders = WhyChooseUsSlider::active()
                    ->ordered()
                    ->get()
                    ->map(function ($slider) {
                        return [
                            'id' => $slider->id,
                            'order' => $slider->order,
                            'image_url' => $slider->image_url,
                            'video_id' => $this->extractYouTubeVideoId($slider->video_link)
                        ];
                    });
                
                return [
                    'why_choose_us' => [
                        'id' => $whyChooseUs->id,
                        'first_icon_url' => $whyChooseUs->first_icon_url,
                        'second_icon_url' => $whyChooseUs->second_icon_url,
                        'third_icon_url' => $whyChooseUs->third_icon_url,
                        'fourth_icon_url' => $whyChooseUs->fourth_icon_url,
                        'content' => $translation ? $translation->content : null
                    ],
                    'sliders' => $sliders
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $data
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving Why Choose Us data: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }

    /**
     * Extract YouTube video ID from various YouTube URL formats
     *
     * @param string|null $url
     * @return string|null
     */
    private function extractYouTubeVideoId(?string $url): ?string
    {
        if (empty($url)) {
            return null;
        }

        // YouTube URL patterns
        $patterns = [
            '/youtube\.com\/watch\?v=([a-zA-Z0-9_-]+)/',
            '/youtu\.be\/([a-zA-Z0-9_-]+)/',
            '/youtube\.com\/embed\/([a-zA-Z0-9_-]+)/',
            '/youtube\.com\/v\/([a-zA-Z0-9_-]+)/',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $url, $matches)) {
                return $matches[1];
            }
        }

        return null;
    }
} 