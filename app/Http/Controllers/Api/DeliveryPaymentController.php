<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\ContentPageResource;
use App\Interfaces\Services\Front\DeliveryPaymentServiceInterface;
use App\Interfaces\Services\Front\LanguageServiceInterface;
use App\Traits\LocaleFromHeader;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class DeliveryPaymentController extends Controller
{
    use LocaleFromHeader;

    protected DeliveryPaymentServiceInterface $deliveryPaymentService;
    protected LanguageServiceInterface $languageService;

    public function __construct(
        DeliveryPaymentServiceInterface $deliveryPaymentService,
        LanguageServiceInterface $languageService
    ) {
        $this->deliveryPaymentService = $deliveryPaymentService;
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

            $deliveryPayment = Cache::rememberForever("delivery_payment_{$languageId}", function () use ($languageId) {
                return $this->deliveryPaymentService->first(['translates' => fn($q) => $q->where('language_id', $languageId)]);
            });

            if (!$deliveryPayment) {
                return $this->notFound('Delivery payment not found');
            }

            return $this->success(new ContentPageResource($deliveryPayment), 'Delivery payment retrieved successfully');

        } catch (\Exception $e) {
            return $this->serverError('Error retrieving delivery payment: ' . $e->getMessage());
        }
    }
}
