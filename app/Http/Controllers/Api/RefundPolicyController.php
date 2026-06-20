<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\ContentPageResource;
use App\Interfaces\Services\Front\RefundPolicyServiceInterface;
use App\Models\Language;
use App\Traits\LocaleFromHeader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class RefundPolicyController extends Controller
{
    use LocaleFromHeader;

    protected RefundPolicyServiceInterface $refundPolicyService;

    public function __construct(RefundPolicyServiceInterface $refundPolicyService)
    {
        $this->refundPolicyService = $refundPolicyService;
    }

    public function index(Request $request)
    {
        try {
            $languageId = $this->getLanguageIdFromHeader($request);

            $language = Language::find($languageId);
            if (!$language) {
                return response()->json(['success' => false, 'message' => 'Invalid language ID'], 400);
            }

            $refundPolicy = Cache::rememberForever("refund_policy_{$languageId}", function () use ($languageId) {
                return $this->refundPolicyService->first(['translates' => fn($q) => $q->where('language_id', $languageId)]);
            });

            if (!$refundPolicy) {
                return response()->json(['success' => false, 'message' => 'Refund policy not found'], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Refund policy retrieved successfully',
                'data' => new ContentPageResource($refundPolicy),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving refund policy: ' . $e->getMessage(),
            ], 500);
        }
    }
}
