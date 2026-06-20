<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\ContentPageResource;
use App\Interfaces\Services\Front\PrivacyPolicyServiceInterface;
use App\Models\Language;
use App\Traits\LocaleFromHeader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PrivacyPolicyController extends Controller
{
    use LocaleFromHeader;

    protected PrivacyPolicyServiceInterface $privacyPolicyService;

    public function __construct(PrivacyPolicyServiceInterface $privacyPolicyService)
    {
        $this->privacyPolicyService = $privacyPolicyService;
    }

    public function index(Request $request)
    {
        try {
            $languageId = $this->getLanguageIdFromHeader($request);

            $language = Language::find($languageId);
            if (!$language) {
                return response()->json(['success' => false, 'message' => 'Invalid language ID'], 400);
            }

            $privacyPolicy = Cache::rememberForever("privacy_policy_{$languageId}", function () use ($languageId) {
                return $this->privacyPolicyService->first(['translates' => fn($q) => $q->where('language_id', $languageId)]);
            });

            if (!$privacyPolicy) {
                return response()->json(['success' => false, 'message' => 'Privacy policy not found'], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Privacy policy retrieved successfully',
                'data' => new ContentPageResource($privacyPolicy),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving privacy policy: ' . $e->getMessage(),
            ], 500);
        }
    }
}
