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

    public function index(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $languageId = $this->getLanguageIdFromHeader($request);

            $language = $this->languageService->find($languageId);
            if (!$language) {
                return $this->error('Invalid language ID', 400);
            }

            $warrantyPolicy = Cache::rememberForever("warranty_policy_{$languageId}", function () use ($languageId) {
                return $this->warrantyPolicyService->first(['translates' => fn($q) => $q->where('language_id', $languageId)]);
            });

            if (!$warrantyPolicy) {
                return $this->notFound('Warranty policy not found');
            }

            return $this->success(new ContentPageResource($warrantyPolicy), 'Warranty policy retrieved successfully');

        } catch (\Exception $e) {
            return $this->serverError('Error retrieving warranty policy: ' . $e->getMessage());
        }
    }
}
