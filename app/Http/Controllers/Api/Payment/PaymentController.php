<?php

namespace App\Http\Controllers\Api\Payment;

use App\Http\Controllers\Controller;
use App\Enums\DeliveryMethod;
use App\Enums\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Order;
use App\Traits\LocaleFromHeader;

class PaymentController extends Controller
{
    use LocaleFromHeader;

    /**
     * Payment və Delivery üçün map-lar (enum əvəzinə sadə array)
     */
    private array $paymentMethods = [
        1 => 'cash',
        2 => 'installment',
    ];

    private array $deliveryMethods = [
        1 => 'courier',
        2 => 'pickup',
        3 => 'express',
    ];

    /**
     * Mövcud sifariş üçün nağd ödənişi təsdiqlə
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
            'message' => 'Nağd ödəniş uğurla təsdiqləndi',
            'data' => [
                'order_id' => $order->id,
                'order_number' => $order->order_id,
                'payment_status' => 'pending',
                'status' => 'processing'
            ]
        ]);
    }

    /**
     * Mövcud sifariş üçün kredit ödənişini təsdiqlə
     */
    public function startInstallment(Request $request)
    {
        $data = $request->validate([
            'order_id' => 'required|exists:orders,id',
        ]);

        $order = Order::findOrFail($data['order_id']);

        $order->update([
            'payment_method' => 'installment',
            'payment_status' => 'pending',
            'status' => 'processing',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Kredit ödənişi uğurla təsdiqləndi',
            'data' => [
                'order_id' => $order->id,
                'order_number' => $order->order_id,
                'payment_status' => 'pending',
                'status' => 'processing',
                'cancelUrl' => route('payment.cancel', ['order_id' => $order->id], true),
                'declineUrl' => route('payment.decline', ['order_id' => $order->id], true),
            ]
        ]);
    }

    /**
     * Yeni əlavə olunan metod — order_id-yə görə payment başlat
     */
    public function pay(Request $request)
    {
        $data = $request->validate([
            'order_id' => 'required|exists:orders,id',
        ]);

        $order = Order::findOrFail($data['order_id']);

        // Order-da payment_method integer şəklində saxlanılırsa:
        $methodInt = is_numeric($order->payment_method)
            ? (int) $order->payment_method
            : array_search($order->payment_method, $this->paymentMethods);

        $method = $this->paymentMethods[$methodInt] ?? null;

        if (!$method) {
            return response()->json([
                'success' => false,
                'message' => 'Ödəniş üsulu tanınmadı'
            ], 400);
        }

        // Seçilən metoda görə yönləndiririk
        if ($method === 'cash') {
            return $this->startCash($request);
        } elseif ($method === 'installment') {
            return $this->startInstallment($request);
        }

        return response()->json([
            'success' => false,
            'message' => 'Uyğun ödəniş metodu tapılmadı'
        ], 400);
    }

    /**
     * Success nəticəsi
     */
    public function success(Request $request)
    {
        $orderId = $request->query('order_id');
        $order = Order::find($orderId);

        if ($order) {
            $order->update([
                'status' => 'completed',
                'payment_status' => 'paid'
            ]);
        }

        $data = $this->decodeUp($request->query('up'));
        Log::info('Payment Success', ['order_id' => $orderId, 'data' => $data]);

        return view('payment.success', compact('order', 'data'));
    }

    /**
     * Cancel nəticəsi
     */
    public function cancel(Request $request)
    {
        $orderId = $request->query('order_id');
        $order = Order::find($orderId);

        if ($order) {
            $order->update([
                'status' => 'cancelled',
                'payment_status' => 'unpaid'
            ]);
        }

        $data = $this->decodeUp($request->query('up'));
        Log::info('Payment Cancel', ['order_id' => $orderId, 'data' => $data]);

        return view('payment.cancel', compact('order', 'data'));
    }

    /**
     * Decline nəticəsi
     */
    public function decline(Request $request)
    {
        $orderId = $request->query('order_id');
        $order = Order::find($orderId);

        if ($order) {
            $order->update([
                'status' => 'failed',
                'payment_status' => 'failed'
            ]);
        }

        $data = $this->decodeUp($request->query('up'));
        Log::info('Payment Decline', ['order_id' => $orderId, 'data' => $data]);

        return view('payment.decline', compact('order', 'data'));
    }

    /**
     * Müvəqqəti sifariş yaradılması (map-lar istifadə olunur)
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
                'selected_card'   => 'nullable|string|max:255',
            ]);

            $deliveryMethodName = $this->deliveryMethods[$data['delivery_method']];
            $paymentMethodName  = $this->paymentMethods[$data['payment_method']];

            $monthlyPrice = isset($data['months']) && $data['months'] > 0
                ? ($data['amount'] / $data['months'])
                : $data['amount'];

            $userId = optional($request->user())->id;
            $memberId = $userId ?? mt_rand(1000000000, 9999999999);

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
                    'selected_card'   => $data['selected_card'] ?? null,
                    'month'           => $data['months'],
                    'monthly_price'   => $monthlyPrice,
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
                    'selected_card'   => $data['selected_card'] ?? null,
                    'month'           => $data['months'],
                    'monthly_price'   => $monthlyPrice,
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
                ]
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Temporary order creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Müvəqqəti sifariş yaradılarkən xəta baş verdi'
            ], 500);
        }
    }

    private function decodeUp(?string $base64): array
    {
        if (!$base64) return [];
        try {
            $json = base64_decode($base64, true);
            return json_decode($json, true) ?: [];
        } catch (\Throwable $e) {
            Log::error('UP decode error: '.$e->getMessage());
            return [];
        }
    }
}
