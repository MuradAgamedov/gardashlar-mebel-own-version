<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\ContentPageResource;
use App\Interfaces\Services\Front\SiteUsageRulesServiceInterface;
use App\Interfaces\Services\Front\LanguageServiceInterface;
use App\Traits\LocaleFromHeader;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class SiteUsageRulesController extends Controller
{
    use LocaleFromHeader;

    protected SiteUsageRulesServiceInterface $siteUsageRulesService;
    protected LanguageServiceInterface $languageService;

    public function __construct(
        SiteUsageRulesServiceInterface $siteUsageRulesService,
        LanguageServiceInterface $languageService
    ) {
        $this->siteUsageRulesService = $siteUsageRulesService;
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

            $siteUsageRules = Cache::rememberForever("site_usage_rules_{$languageId}", function () use ($languageId) {
                return $this->siteUsageRulesService->first(['translates' => fn($q) => $q->where('language_id', $languageId)]);
            });

            if (!$siteUsageRules) {
                return $this->notFound('Site usage rules not found');
            }

            return $this->success(new ContentPageResource($siteUsageRules), 'Site usage rules retrieved successfully');

        } catch (\Exception $e) {
            return $this->serverError('Error retrieving site usage rules: ' . $e->getMessage());
        }
    }
}
