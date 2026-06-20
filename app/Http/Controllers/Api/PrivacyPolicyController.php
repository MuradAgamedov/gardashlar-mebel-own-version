<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\ContentPageResource;
use App\Interfaces\Services\Front\PrivacyPolicyServiceInterface;
use App\Interfaces\Services\Front\LanguageServiceInterface;
use App\Traits\LocaleFromHeader;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class PrivacyPolicyController extends Controller
{
    use LocaleFromHeader;

    protected PrivacyPolicyServiceInterface $privacyPolicyService;
    protected LanguageServiceInterface $languageService;

    public function __construct(
        PrivacyPolicyServiceInterface $privacyPolicyService,
        LanguageServiceInterface $languageService
    ) {
        $this->privacyPolicyService = $privacyPolicyService;
        $this->languageService = $languageService;
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $languageId = $this->getLanguageIdFromHeader($request);

            $language = $this->languageService->first([], ['id' => $languageId]);
            if (!$language) {
                return $this->error('Invalid language ID', 400);
            }

            $privacyPolicy = Cache::rememberForever("privacy_policy_{$languageId}", function () use ($languageId) {
                return $this->privacyPolicyService->first(['translates' => fn($q) => $q->where('language_id', $languageId)]);
            });

            if (!$privacyPolicy) {
                return $this->notFound('Privacy policy not found');
            }

            return $this->success(new ContentPageResource($privacyPolicy), 'Privacy policy retrieved successfully');

        } catch (\Exception $e) {
            return $this->serverError('Error retrieving privacy policy: ' . $e->getMessage());
        }
    }
}
