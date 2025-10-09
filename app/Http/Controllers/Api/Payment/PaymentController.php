<?php

namespace App\Http\Controllers\Api\Payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\Order;
use App\Models\CreditCard;
use App\Traits\LocaleFromHeader;
use App\Helpers\PaymentLoginHelper;

class PaymentController extends Controller
{
    use LocaleFromHeader;

    private array $paymentMethods = [
        1 => 'cash',
        2 => 'online',
        3 => 'installment',
    ];

    private array $deliveryMethods = [
        1 => 'store',
        2 => 'delivery',
    ];

    /**
     * 🔹 Nağd ödəniş
     */
    public function startCash(Request $request)
    {
        $data = $request->validate([
            'order_id' => 'required|exists:orders,id',
        ]);

        $order = Order::findOrFail($data['order_id']);

        $order->update([
            'payment_method' => 'cash',
            'payment_status' => 'pending',
            'status' => 'processing',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Nağd ödəniş təsdiqləndi',
            'data' => [
                'order_id' => $order->id,
                'order_number' => $order->order_id,
                'status' => 'processing',
                'payment_status' => 'pending',
            ]
        ]);
    }

    /**
     * 🔹 Online ödəniş
     */
    public function startOnline(Request $request)
    {
        $data = $request->validate([
            'order_id' => 'required|exists:orders,id',
        ]);

        $order = Order::findOrFail($data['order_id']);

        $baseUrl = env('UNITED_PAYMENT_API_URL', 'https://test-vpos.unitedpayment.az/api');

        $payload = [
            'amount'     => (float) $order->amount,
            'memberId'   => $order->selected_card,
            'language'   => $order->language ?? 'EN',
            'successUrl' => route('payment.success', ['order_id' => $order->id], true),
            'cancelUrl'  => route('payment.cancel', ['order_id' => $order->id], true),
            'declineUrl' => route('payment.decline', ['order_id' => $order->id], true),
        ];

        $order->update([
            'payment_method' => 'online',
            'payment_status' => 'pending',
            'status' => 'processing',
        ]);

        $response = PaymentLoginHelper::withAuth()->post($baseUrl.'/transactions/pay', $payload);

        if ($response->successful()) {
            $body = $response->json();
            $redirectUrl = $body['url'] ?? null;

            if ($redirectUrl) {
                return response()->json([
                    'success' => true,
                    'redirect_url' => $redirectUrl,
                    'order_id' => $order->id,
                ]);
            }

            Log::error('Online Payment: URL not found', ['body' => $response->body()]);
            return response()->json(['success' => false, 'message' => 'Ödəniş linki tapılmadı'], 422);
        }

        Log::error('Online Payment failed', ['body' => $response->body()]);
        return response()->json(['success' => false, 'message' => 'Ödəniş sorğusu uğursuz oldu'], 500);
    }

    /**
     * 🔹 Kredit (taksit) ödənişi
     */
    public function startInstallment(Request $request)
    {
        $data = $request->validate([
            'order_id' => 'required|exists:orders,id',
        ]);

        $order = Order::findOrFail($data['order_id']);

        $baseUrl = env('UNITED_PAYMENT_API_URL', 'https://test-vpos.unitedpayment.az/api');

        $payload = [
            'amount'      => (float) $order->amount,
            'memberId'    => $order->selected_card,
            'language'    => $order->language ?? 'EN',
            'installment' => (int) $order->month,
            'successUrl'  => route('payment.success', ['order_id' => $order->id], true),
            'cancelUrl'   => route('payment.cancel', ['order_id' => $order->id], true),
            'declineUrl'  => route('payment.decline', ['order_id' => $order->id], true),
        ];

        $order->update([
            'payment_method' => 'installment',
            'payment_status' => 'pending',
            'status'         => 'processing',
        ]);

        $response = PaymentLoginHelper::withAuth()->post($baseUrl.'/transactions/taksit', $payload);

        if ($response->successful()) {
            $body = $response->json();
            $redirectUrl = $body['url'] ?? null;

            if ($redirectUrl) {
                return response()->json([
                    'success' => true,
                    'redirect_url' => $redirectUrl,
                    'order_id' => $order->id,
                ]);
            }

            Log::error('Installment: URL not found', ['body' => $response->body()]);
            return response()->json(['success' => false, 'message' => 'Ödəniş linki tapılmadı'], 422);
        }

        Log::error('Installment failed', ['body' => $response->body()]);
        return response()->json(['success' => false, 'message' => 'Ödəniş sorğusu uğursuz oldu'], 500);
    }

    /**
     * 🔹 payment_method-ə görə yönləndirmə
     */
    public function pay(Request $request)
    {
        $data = $request->validate([
            'order_id' => 'required|exists:orders,id',
        ]);

        $order = Order::findOrFail($data['order_id']);
        $method = is_numeric($order->payment_method)
            ? $this->paymentMethods[(int)$order->payment_method]
            : $order->payment_method;

        return match ($method) {
            'cash'        => $this->startCash($request),
            'online'      => $this->startOnline($request),
            'installment' => $this->startInstallment($request),
            default => response()->json([
                'success' => false,
                'message' => 'Uyğun ödəniş metodu tapılmadı',
            ], 400),
        };
    }

    /**
     * 🔹 Müvəqqəti sifariş yaradılması
     */
    public function createTemporaryOrder(Request $request)
    {
        try {
            $data = $request->validate([
                'order_id'        => 'nullable|string|exists:orders,order_id',
                'product_ids'     => 'required|array|min:1',
                'product_ids.*'   => 'exists:products,id',
                'full_name'       => 'required|string|max:255',
                'phone'           => 'required|string|max:20',
                'email'           => 'required|email|max:255',
                'city'            => 'required|string|max:255',
                'address'         => 'nullable|string',
                'amount'          => 'nullable|numeric|min:1',
                'months'          => 'nullable|in:2,3,6,9,12',
                'language'        => 'nullable|in:AZ,EN,RU',
                'delivery_method' => 'required|integer|in:' . implode(',', array_keys($this->deliveryMethods)),
                'payment_method'  => 'required|integer|in:' . implode(',', array_keys($this->paymentMethods)),
                'selected_card'   => 'nullable|integer|exists:credit_cards,id',
            ]);

            $deliveryMethodName = $this->deliveryMethods[$data['delivery_method']];
            $paymentMethodName  = $this->paymentMethods[$data['payment_method']];

            $cardTitle = null;
            if (!empty($data['selected_card'])) {
                $card = CreditCard::find($data['selected_card']);
                $cardTitle = $card?->title;
            }

            $monthlyPrice = isset($data['months']) && $data['months'] > 0
                ? ($data['amount'] / $data['months'])
                : $data['amount'];

            $userId = optional($request->user())->id;

            if (!empty($data['order_id'])) {
                $order = Order::where('order_id', $data['order_id'])->firstOrFail();
                $order->update([
                    'product_ids'     => json_encode($data['product_ids']),
                    'full_name'       => $data['full_name'],
                    'phone'           => $data['phone'],
                    'email'           => $data['email'],
                    'city'            => $data['city'],
                    'address'         => $data['address'] ?? null,
                    'delivery_method' => $deliveryMethodName,
                    'payment_method'  => $paymentMethodName,
                    'selected_card'   => $cardTitle,
                    'month'           => $data['months'],
                    'monthly_price'   => $monthlyPrice,
                    'amount'          => $data['amount'],
                    'language'        => $data['language'] ?? 'EN',
                    'status'          => 'temporary',
                    'payment_status'  => 'unpaid',
                ]);
                $message = 'Mövcud sifariş yeniləndi';
            } else {
                $order = Order::create([
                    'user_id'         => $userId,
                    'product_ids'     => json_encode($data['product_ids']),
                    'full_name'       => $data['full_name'],
                    'phone'           => $data['phone'],
                    'email'           => $data['email'],
                    'city'            => $data['city'],
                    'address'         => $data['address'] ?? null,
                    'order_id'        => strtoupper(Str::random(12)),
                    'delivery_method' => $deliveryMethodName,
                    'payment_method'  => $paymentMethodName,
                    'selected_card'   => $cardTitle,
                    'month'           => $data['months'],
                    'monthly_price'   => $monthlyPrice,
                    'amount'          => $data['amount'],
                    'language'        => $data['language'] ?? 'EN',
                    'status'          => 'temporary',
                    'payment_status'  => 'unpaid',
                ]);
                $message = 'Yeni müvəqqəti sifariş yaradıldı';
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => [
                    'order_id' => $order->id,
                    'payment_method' => $paymentMethodName,
                    'delivery_method' => $deliveryMethodName,
                    'selected_card' => $cardTitle,
                ]
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Temporary order creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Müvəqqəti sifariş yaradılarkən xəta baş verdi',
            ], 500);
        }
    }
}
