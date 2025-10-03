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
            'successUrl'  => env('PAYMENT_SUCCESS_URL', 'https://your-frontend.com/payment-success'),
            'cancelUrl'   => env('PAYMENT_CANCEL_URL', 'https://your-frontend.com/payment-cancel'),
            'declineUrl'  => env('PAYMENT_DECLINE_URL', 'https://your-frontend.com/payment-decline'),
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
     * Bankdan callback (success/cancel/decline)
     */
    public function callback(Request $request)
    {
        $result = $request->query('result');
        $up     = $request->query('up');

        $decoded = null;
        if ($up) {
            $decoded = $this->decodeUp($up);
            Log::info('UP callback decoded', ['data' => $decoded]);
        }

        return response()->json([
            'success' => $result === 'success',
            'status'  => $result,
            'data'    => $decoded,
        ]);
    }

    private function decodeUp(string $base64)
    {
        try {
            $json = base64_decode($base64, true);
            if ($json === false) return null;
            $arr = json_decode($json, true);
            return is_array($arr) ? $arr : null;
        } catch (\Throwable $e) {
            Log::error('UP decode error: '.$e->getMessage());
            return null;
        }
    }
}
