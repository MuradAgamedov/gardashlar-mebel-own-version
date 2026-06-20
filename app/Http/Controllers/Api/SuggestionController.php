<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Interfaces\Services\Front\SuggestionServiceInterface;
use App\Interfaces\Services\Front\LanguageServiceInterface;
use App\Http\Resources\Api\SuggestionResource;
use App\Traits\LocaleFromHeader;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class SuggestionController extends Controller
{
    use LocaleFromHeader;

    protected SuggestionServiceInterface $suggestionService;
    protected LanguageServiceInterface $languageService;

    public function __construct(
        SuggestionServiceInterface $suggestionService,
        LanguageServiceInterface $languageService
    ) {
        $this->suggestionService = $suggestionService;
        $this->languageService = $languageService;
    }
   
    public function index(Request $request): JsonResponse
    {
        try {
            $languageId = $this->getLanguageIdFromHeader($request);
            $language = $this->languageService->find($languageId);
            
            if (!$language) {
                return $this->error('Invalid language ID', 400);
            }

            $suggestions = Cache::rememberForever("suggestions_{$languageId}", function () use ($languageId) {
                return $this->suggestionService->getActiveOrderedWithTranslations($languageId);
            });

            return $this->success(SuggestionResource::collection($suggestions), 'Suggestions retrieved successfully');

        } catch (\Exception $e) {
            return $this->serverError('Error retrieving suggestions: ' . $e->getMessage());
        }
    }

    public function show(Request $request, $id): JsonResponse
    {
        try {
            $languageId = $this->getLanguageIdFromHeader($request);
            $language = $this->languageService->find($languageId);
            
            if (!$language) {
                return $this->error('Invalid language ID', 400);
            }

            $suggestion = Cache::rememberForever("suggestion_{$id}_{$languageId}", function () use ($id, $languageId) {
                return $this->suggestionService->first([
                    'translates' => fn($q) => $q->where('language_id', $languageId)
                ], [
                    'id' => $id,
                    'status' => true
                ]);
            });

            if (!$suggestion) {
                return $this->notFound('Suggestion not found');
            }

            return $this->success(new SuggestionResource($suggestion), 'Suggestion retrieved successfully');

        } catch (\Exception $e) {
            return $this->serverError('Error retrieving suggestion: ' . $e->getMessage());
        }
    }
} 