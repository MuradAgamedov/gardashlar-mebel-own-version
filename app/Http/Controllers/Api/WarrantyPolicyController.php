<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\ContentPageResource;
use App\Interfaces\Services\Front\WarrantyPolicyServiceInterface;
use App\Interfaces\Services\Front\LanguageServiceInterface;
use App\Traits\LocaleFromHeader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class WarrantyPolicyController extends Controller
{
    use LocaleFromHeader;

    protected WarrantyPolicyServiceInterface $warrantyPolicyService;
    protected LanguageServiceInterface $languageService;

    public function __construct(
        WarrantyPolicyServiceInterface $warrantyPolicyService,
        LanguageServiceInterface $languageService
    ) {
        $this->warrantyPolicyService = $warrantyPolicyService;
        $this->languageService = $languageService;
    }

    public function index(Request $request)
    {
        try {
            $languageId = $this->getLanguageIdFromHeader($request);

            $language = $this->languageService->find($languageId);
            if (!$language) {
                return response()->json(['success' => false, 'message' => 'Invalid language ID'], 400);
            }

            $warrantyPolicy = Cache::rememberForever("warranty_policy_{$languageId}", function () use ($languageId) {
                return $this->warrantyPolicyService->first(['translates' => fn($q) => $q->where('language_id', $languageId)]);
            });

            if (!$warrantyPolicy) {
                return response()->json(['success' => false, 'message' => 'Warranty policy not found'], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Warranty policy retrieved successfully',
                'data' => new ContentPageResource($warrantyPolicy),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving warranty policy: ' . $e->getMessage(),
            ], 500);
        }
    }
}
