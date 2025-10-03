<?php

namespace App\Http\Controllers\Api\Payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\PaymentLoginHelper;
use App\Models\Order;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    /**
     * Installment əməliyyatını başlat
     */
    public function startInstallment(Request $request)
    {
        $data = $request->validate([
            'full_name'       => 'required|string|max:255',
            'phone'           => 'required|string|max:50',
            'email'           => 'required|email|max:255',
            'city'            => 'required|string|max:255',
            'amount'          => 'required|numeric|min:1',
            'months'          => 'required|in:2,3,6,9,12',
            'member_id'       => 'required|string',
            'language'        => 'nullable|in:AZ,EN,RU',
        ]);

        $lang = $data['language'] ?? 'EN';
        $baseUrl = env('UNITED_PAYMENT_API_URL', 'https://test-vpos.unitedpayment.az/api');

        // 🔹 Order yaradılır (pending, unpaid)
        $order = Order::create([
            'user_id'        => $request->user()->id ?? null,
            'full_name'      => $data['full_name'],
            'phone'          => $data['phone'],
            'email'          => $data['email'],
            'city'           => $data['city'],
            'order_id'       => strtoupper(Str::random(12)),
            'delivery_method'=> 'installment',
            'payment_method' => 'card',
            'selected_card'  => $data['member_id'],
            'month'          => $data['months'],
            'monthly_price'  => $data['amount'] / $data['months'],
            'status'         => 'pending',
            'payment_status' => 'unpaid',
        ]);

        $payload = [
            'amount'      => (float) $data['amount'],
            'memberId'    => $data['member_id'],
            'language'    => $lang,
            'installment' => (int) $data['months'],
            'successUrl'  => route('payment.success', ['order_id' => $order->id], true),
            'cancelUrl'   => route('payment.cancel', ['order_id' => $order->id], true),
            'declineUrl'  => route('payment.decline', ['order_id' => $order->id], true),
        ];

        $response = PaymentLoginHelper::withAuth()->post($baseUrl.'/transactions/taksit', $payload);

        if ($response->successful()) {
            $body = $response->json();
            $redirectUrl = is_array($body) ? ($body['url'] ?? null) : (string) $response->body();

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
     * Nağd ödəniş (birdəfəlik)
     */
    public function startCash(Request $request)
    {
        $data = $request->validate([
            'full_name'       => 'required|string|max:255',
            'phone'           => 'required|string|max:50',
            'email'           => 'required|email|max:255',
            'city'            => 'required|string|max:255',
            'amount'          => 'required|numeric|min:1',
            'member_id'       => 'required|string',
            'language'        => 'nullable|in:AZ,EN,RU',
        ]);

        $lang = $data['language'] ?? 'EN';
        $baseUrl = env('UNITED_PAYMENT_API_URL', 'https://test-vpos.unitedpayment.az/api');

        // 🔹 Order yaradılır (pending, unpaid)
        $order = Order::create([
            'user_id'        => $request->user()->id ?? null,
            'full_name'      => $data['full_name'],
            'phone'          => $data['phone'],
            'email'          => $data['email'],
            'city'           => $data['city'],
            'order_id'       => strtoupper(Str::random(12)),
            'delivery_method'=> 'cash',
            'payment_method' => 'cash',
            'selected_card'  => $data['member_id'],
            'status'         => 'pending',
            'payment_status' => 'unpaid',
        ]);

        $payload = [
            'amount'     => (float) $data['amount'],
            'memberId'   => $data['member_id'],
            'language'   => $lang,
            'successUrl' => route('payment.success', ['order_id' => $order->id], true),
            'cancelUrl'  => route('payment.cancel', ['order_id' => $order->id], true),
            'declineUrl' => route('payment.decline', ['order_id' => $order->id], true),
        ];

        $response = PaymentLoginHelper::withAuth()->post($baseUrl.'/transactions/pay', $payload);

        if ($response->successful()) {
            $body = $response->json();
            $redirectUrl = is_array($body) ? ($body['url'] ?? null) : (string) $response->body();

            if ($redirectUrl) {
                return response()->json([
                    'success' => true,
                    'redirect_url' => $redirectUrl,
                    'order_id' => $order->id,
                ]);
            }

            Log::error('Cash Payment: URL not found', ['body' => $response->body()]);
            return response()->json(['success' => false, 'message' => 'Ödəniş linki tapılmadı'], 422);
        }

        Log::error('Cash Payment failed', ['body' => $response->body()]);
        return response()->json(['success' => false, 'message' => 'Ödəniş sorğusu uğursuz oldu'], 500);
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
