<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\Api\FaqResource;

use App\Http\Controllers\Controller;
use App\Interfaces\Services\Front\FaqServiceInterface;
use App\Traits\LocaleFromHeader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;


class FaqController extends Controller
{
    use LocaleFromHeader;

    protected FaqServiceInterface $faqService;

    public function __construct(FaqServiceInterface $faqService)
    {
        $this->faqService = $faqService;
    }


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