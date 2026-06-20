<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\ContentPageResource;
use App\Interfaces\Services\Front\RefundPolicyServiceInterface;
use App\Interfaces\Services\Front\LanguageServiceInterface;
use App\Traits\LocaleFromHeader;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class RefundPolicyController extends Controller
{
    use LocaleFromHeader;

    protected RefundPolicyServiceInterface $refundPolicyService;
    protected LanguageServiceInterface $languageService;

    public function __construct(
        RefundPolicyServiceInterface $refundPolicyService,
        LanguageServiceInterface $languageService
    ) {
        $this->refundPolicyService = $refundPolicyService;
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

            $refundPolicy = Cache::rememberForever("refund_policy_{$languageId}", function () use ($languageId) {
                return $this->refundPolicyService->first(['translates' => fn($q) => $q->where('language_id', $languageId)]);
            });

            if (!$refundPolicy) {
                return $this->notFound('Refund policy not found');
            }

            return $this->success(new ContentPageResource($refundPolicy), 'Refund policy retrieved successfully');

        } catch (\Exception $e) {
            return $this->serverError('Error retrieving refund policy: ' . $e->getMessage());
        }
    }
}
