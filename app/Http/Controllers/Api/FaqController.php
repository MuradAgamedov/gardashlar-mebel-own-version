<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\Api\FaqResource;

use App\Http\Controllers\Controller;
use App\Interfaces\Services\Front\FaqServiceInterface;
use App\Traits\LocaleFromHeader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

/**
 * @OA\Tag(
 *     name="FAQs",
 *     description="API Endpoints for FAQs"
 * )
 */
class FaqController extends Controller
{
    use LocaleFromHeader;

    protected FaqServiceInterface $faqService;

    public function __construct(FaqServiceInterface $faqService)
    {
        $this->faqService = $faqService;
    }


    /**
     * @OA\Get(
     *     path="/api/faqs",
     *     summary="Get all active FAQs",
     *     description="Returns list of active FAQs with translated content based on header language",
     *     tags={"FAQs"},
     *     @OA\Parameter(
     *         name="locale",
     *         in="header",
     *         description="Language ID (1=Az, 2=En, 3=Ru)",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="FAQs retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="FAQs retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="question", type="string", example="Özəl mebel sifarişləri hansı müddətə təhvil verilir?"),
     *                     @OA\Property(property="answer", type="string", example="Özəl mebel sifarişlərinə müştərinin istəklərinə və sifarişin dizayn formasna və həcminə görə istehsal müddəti təyin olunur.")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No FAQs found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="No FAQs found")
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        $languageId = $this->getLanguageIdFromHeader($request);
        // Debug məlumatları əlavə edirik
        $debug = [
            'requested_locale' => $request->header('locale'),
            'detected_language_id' => $languageId,
            'all_languages' => \App\Models\Language::where('status', true)->get(['id', 'lang_code', 'is_default'])
        ];
        
        $cacheKey = "faqs_{$languageId}";
        
        $faqs = Cache::remember($cacheKey, 3600, function () use ($languageId) {
            return $this->faqService->getActiveOrderedWithTranslations($languageId);
        });
        
        if ($faqs->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No FAQs found',
                'debug' => $debug
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'FAQs retrieved successfully',
            'data' => FaqResource::collection($faqs),
            'debug' => $debug
        ]);
    }
} 