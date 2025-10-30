<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class UserFinProductContoller extends Controller
{
    /**
     * FIN koduna görə məhsul məlumatlarını alır.
     */
    public function index(Request $request)
    {
        $fin = $request->user()->fin;


        if (!$fin) {
            return response()->json(['error' => 'fin is required'], 422);
        }

        try {
            $auth = $this->getCachedFinApiAuthData();

            if (empty($auth['token']) || empty($auth['tenant_id'])) {
                return response()->json(['error' => 'Auth data missing (token or tenant_id)'], 500);
            }

            $baseUrl = rtrim(env('FIN_API_BASE_URL'), '/');
            $invoiceUrl = "{$baseUrl}/sales/invoice-products?fin=" . urlencode($fin) . "&invoiceTypes[]=11&withAttachmentUrl=1";

            $invoiceResponse = Http::withHeaders([
                'Accept' => 'application/json',
                'X-AUTH-PROTOKEN' => $auth['token'],
                'X-TENANT-ID' => $auth['tenant_id'],
            ])->get($invoiceUrl);

            // Əgər token vaxtı bitibsə (401), yenidən login et
            if ($invoiceResponse->status() === 401) {
                Cache::forget('fin_api_auth');
                $auth = $this->getCachedFinApiAuthData();

                $invoiceResponse = Http::withHeaders([
                    'Accept' => 'application/json',
                    'X-AUTH-PROTOKEN' => $auth['token'],
                    'X-TENANT-ID' => $auth['tenant_id'],
                ])->get($invoiceUrl);
            }

            if ($invoiceResponse->successful()) {
                return response()->json($invoiceResponse->json(), 200);
            }

            return response()->json([
                'error' => 'Invoice API request failed',
                'details' => $invoiceResponse->json(),
            ], $invoiceResponse->status());

        } catch (\Throwable $e) {
            return response()->json([
                'error' => 'Unexpected error occurred',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Satış statuslarını gətirir.
     */
    public function getOrderStatuses()
    {
        try {
            $auth = $this->getCachedFinApiAuthData();

            if (empty($auth['token']) || empty($auth['tenant_id'])) {
                return response()->json(['error' => 'Auth data missing (token or tenant_id)'], 500);
            }

            $baseUrl = rtrim(env('FIN_API_BASE_URL'), '/');
            $statusUrl = "{$baseUrl}/sales/status";

            $statusResponse = Http::withHeaders([
                'Accept' => 'application/json',
                'X-AUTH-PROTOKEN' => $auth['token'],
                'X-TENANT-ID' => $auth['tenant_id'],
            ])->get($statusUrl);

            // Token vaxtı bitibsə yenilə
            if ($statusResponse->status() === 401) {
                Cache::forget('fin_api_auth');
                $auth = $this->getCachedFinApiAuthData();

                $statusResponse = Http::withHeaders([
                    'Accept' => 'application/json',
                    'X-AUTH-PROTOKEN' => $auth['token'],
                    'X-TENANT-ID' => $auth['tenant_id'],
                ])->get($statusUrl);
            }

            if ($statusResponse->successful()) {
                return response()->json($statusResponse->json(), 200);
            }

            return response()->json([
                'error' => 'Order status API request failed',
                'details' => $statusResponse->json(),
            ], $statusResponse->status());

        } catch (\Throwable $e) {
            return response()->json([
                'error' => 'Unexpected error occurred',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Token və tenant_id-ni cache-də saxlayır (10 dəq).
     */
    private function getCachedFinApiAuthData(): array
    {
        return Cache::remember('fin_api_auth', now()->addMinutes(10), function () {
            return $this->getFinApiAuthData();
        });
    }

    /**
     * Login edir və həm token, həm də tenant_id qaytarır.
     */
    private function getFinApiAuthData(): array
    {
        $baseUrl = rtrim(env('FIN_API_BASE_URL'), '/');
        $email = env('FIN_API_EMAIL');
        $password = env('FIN_API_PASSWORD');

        if (!$baseUrl || !$email || !$password) {
            throw new \Exception('API credentials missing in .env');
        }

        $loginUrl = "{$baseUrl}/login";

        $response = Http::asJson()->post($loginUrl, [
            'email' => $email,
            'password' => $password,
            'deviceToken' => '',
        ]);

        if ($response->failed()) {
            throw new \Exception('Login request failed: ' . json_encode($response->json()));
        }

        $data = $response->json('data') ?? [];

        return [
            'token' => $data['accessToken'] ?? null,
            'tenant_id' => $data['tenants'][0]['id'] ?? null,
        ];
    }
}
