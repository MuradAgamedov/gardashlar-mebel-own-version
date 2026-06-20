<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\ContentPageResource;
use App\Models\DeliveryPayment;
use App\Models\Language;
use App\Traits\LocaleFromHeader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class DeliveryPaymentController extends Controller
{
    use LocaleFromHeader;

    public function index(Request $request)
    {
        try {
            $languageId = $this->getLanguageIdFromHeader($request);

            $language = Language::find($languageId);
            if (!$language) {
                return response()->json(['success' => false, 'message' => 'Invalid language ID'], 400);
            }

            $deliveryPayment = Cache::rememberForever("delivery_payment_{$languageId}", function () use ($languageId) {
                return DeliveryPayment::with(['translates' => fn($q) => $q->where('language_id', $languageId)])->first();
            });

            if (!$deliveryPayment) {
                return response()->json(['success' => false, 'message' => 'Delivery payment not found'], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Delivery payment retrieved successfully',
                'data' => new ContentPageResource($deliveryPayment),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving delivery payment: ' . $e->getMessage(),
            ], 500);
        }
    }
}
