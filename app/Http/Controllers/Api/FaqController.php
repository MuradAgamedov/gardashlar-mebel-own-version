<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Faq;
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
        
        $cacheKey = "faqs_{$languageId}";
        
        return Cache::remember($cacheKey, 3600, function () use ($languageId) {
            $faqs = Faq::active()
                ->ordered()
                ->with(['translates' => function($query) use ($languageId) {
                    $query->where('language_id', $languageId);
                }])
                ->get();
            
            if ($faqs->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No FAQs found'
                ], 404);
            }
            
            $data = $faqs->map(function ($faq) {
                $translation = $faq->translates->first();
                
                return [
                    'id' => $faq->id,
                    'question' => $translation ? $translation->question : null,
                    'answer' => $translation ? $translation->answer : null,
                ];
            });
            
            return response()->json([
                'success' => true,
                'message' => 'FAQs retrieved successfully',
                'data' => $data
            ]);
        });
    }
} 