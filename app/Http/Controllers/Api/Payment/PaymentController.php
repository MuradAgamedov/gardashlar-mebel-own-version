<?php

namespace App\Http\Controllers\Api\Payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\PaymentLoginHelper;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    /**
     * Installment əməliyyatını başlat
     */
    public function startInstallment(Request $request)
    {
        $data = $request->validate([
            'amount'    => ['required', 'numeric', 'min:1'],
            'months'    => ['required', 'in:2,3,6,9,12'],
            'member_id' => ['required', 'string'],
            'language'  => ['nullable', 'in:AZ,EN,RU'],
        ]);

        $lang = $data['language'] ?? 'EN';
        $baseUrl = env('UNITED_PAYMENT_API_URL', 'https://test-vpos.unitedpayment.az/api');

        $payload = [
            'amount'      => (float) $data['amount'],
            'memberId'    => $data['member_id'],
            'language'    => $lang,
            'installment' => (int) $data['months'],
            'successUrl'  => route('payment.success', [], true),
            'cancelUrl'   => route('payment.cancel', [], true),
            'declineUrl'  => route('payment.decline', [], true),
        ];

        $response = PaymentLoginHelper::withAuth()->post($baseUrl.'/transactions/taksit', $payload);

        if ($response->successful()) {
            $body = $response->json();
            $redirectUrl = is_array($body) ? ($body['url'] ?? null) : (string) $response->body();

            if ($redirectUrl) {
                return response()->json([
                    'success' => true,
                    'redirect_url' => $redirectUrl,
                ]);
            }

            Log::error('Installment: URL not found', ['body' => $response->body()]);
            return response()->json(['success' => false, 'message' => 'Ödəniş linki tapılmadı'], 422);
        }

        Log::error('Installment failed', ['body' => $response->body()]);
        return response()->json(['success' => false, 'message' => 'Ödəniş sorğusu uğursuz oldu'], 500);
    }

    /**
     * Success nəticəsi
     */
    public function success(Request $request)
    {
        $data = $this->decodeUp($request->query('up'));
        Log::info('Payment Success', $data);

        // Laravel blade view göstərə bilərsən:
        return view('payment.success', ['data' => $data]);

        // əgər frontend route-a göndərmək istəsən:
        // return redirect()->away("https://telebe.az/payment-success?status=success");
    }

    /**
     * Cancel nəticəsi
     */
    public function cancel(Request $request)
    {
        $data = $this->decodeUp($request->query('up'));
        Log::info('Payment Cancel', $data);

        return view('payment.cancel', ['data' => $data]);
    }

    /**
     * Decline nəticəsi
     */
    public function decline(Request $request)
    {
        $data = $this->decodeUp($request->query('up'));
        Log::info('Payment Decline', $data);

        return view('payment.decline', ['data' => $data]);
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



    /**
 * Nağd ödəniş (birdəfəlik)
 */
public function startCash(Request $request)
{
    $data = $request->validate([
        'amount'    => ['required', 'numeric', 'min:1'],
        'member_id' => ['required', 'string'],
        'language'  => ['nullable', 'in:AZ,EN,RU'],
    ]);

    $lang = $data['language'] ?? 'EN';
    $baseUrl = env('UNITED_PAYMENT_API_URL', 'https://test-vpos.unitedpayment.az/api');

    $payload = [
        'amount'     => (float) $data['amount'],
        'memberId'   => $data['member_id'],
        'language'   => $lang,
        'successUrl' => route('payment.success', [], true),
        'cancelUrl'  => route('payment.cancel', [], true),
        'declineUrl' => route('payment.decline', [], true),
    ];

    $response = PaymentLoginHelper::withAuth()->post($baseUrl.'/transactions/pay', $payload);

    if ($response->successful()) {
        $body = $response->json();
        $redirectUrl = is_array($body) ? ($body['url'] ?? null) : (string) $response->body();

        if ($redirectUrl) {
            return response()->json([
                'success' => true,
                'redirect_url' => $redirectUrl,
            ]);
        }

        Log::error('Cash Payment: URL not found', ['body' => $response->body()]);
        return response()->json(['success' => false, 'message' => 'Ödəniş linki tapılmadı'], 422);
    }

    Log::error('Cash Payment failed', ['body' => $response->body()]);
    return response()->json(['success' => false, 'message' => 'Ödəniş sorğusu uğursuz oldu'], 500);
}

}
