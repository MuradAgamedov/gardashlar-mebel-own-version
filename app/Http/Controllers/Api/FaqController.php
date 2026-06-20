<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\Api\FaqResource;
use App\Http\Controllers\Controller;
use App\Interfaces\Services\Front\FaqServiceInterface;
use App\Interfaces\Services\Front\LanguageServiceInterface;
use App\Traits\LocaleFromHeader;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class FaqController extends Controller
{
    use LocaleFromHeader;

    protected FaqServiceInterface $faqService;
    protected LanguageServiceInterface $languageService;

    public function __construct(
        FaqServiceInterface $faqService,
        LanguageServiceInterface $languageService
    ) {
        $this->faqService = $faqService;
        $this->languageService = $languageService;
    }

    public function index(Request $request): JsonResponse
    {
        $languageId = $this->getLanguageIdFromHeader($request);
        
        $cacheKey = "faqs_{$languageId}";
        
        $faqs = Cache::remember($cacheKey, 3600, function () use ($languageId) {
            return $this->faqService->getActiveOrderedWithTranslations($languageId);
        });
        
        if ($faqs->isEmpty()) {
            return $this->notFound('No FAQs found');
        }

        return $this->success(FaqResource::collection($faqs), 'FAQs retrieved successfully');
    }
} 