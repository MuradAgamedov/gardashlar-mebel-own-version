<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\ContentPageResource;
use App\Interfaces\Services\Front\SiteUsageRulesServiceInterface;
use App\Models\Language;
use App\Traits\LocaleFromHeader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SiteUsageRulesController extends Controller
{
    use LocaleFromHeader;

    protected SiteUsageRulesServiceInterface $siteUsageRulesService;

    public function __construct(SiteUsageRulesServiceInterface $siteUsageRulesService)
    {
        $this->siteUsageRulesService = $siteUsageRulesService;
    }

    public function index(Request $request)
    {
        try {
            $languageId = $this->getLanguageIdFromHeader($request);

            $language = Language::find($languageId);
            if (!$language) {
                return response()->json(['success' => false, 'message' => 'Invalid language ID'], 400);
            }

            $siteUsageRules = Cache::rememberForever("site_usage_rules_{$languageId}", function () use ($languageId) {
                return $this->siteUsageRulesService->first(['translates' => fn($q) => $q->where('language_id', $languageId)]);
            });

            if (!$siteUsageRules) {
                return response()->json(['success' => false, 'message' => 'Site usage rules not found'], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Site usage rules retrieved successfully',
                'data' => new ContentPageResource($siteUsageRules),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving site usage rules: ' . $e->getMessage(),
            ], 500);
        }
    }
}
