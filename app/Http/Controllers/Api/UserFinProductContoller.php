<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class UserFinProductController extends Controller
{
    /**
     * Əsas endpoint: FIN koduna görə məhsul məlumatlarını alır.
     */
    public function index(Request $request)
    {
        $fin = $request->input('fin');

        if (!$fin) {
            return response()->json(['error' => 'fin is required'], 422);
        }

        try {
            // 1️⃣ Token al (login API vasitəsilə)
            $token = $this->getFinApiToken();

            if (!$token) {
                return response()->json(['error' => 'Token could not be retrieved'], 500);
            }

            // 2️⃣ FIN koduna əsasən məlumat al
            $baseUrl = rtrim(env('FIN_API_BASE_URL'), '/');
            $invoiceUrl = "{$baseUrl}/sales/invoice-products?fin=" . urlencode($fin);

            $invoiceResponse = Http::withToken($token)
                ->acceptJson()
                ->get($invoiceUrl);

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
     * Gizli login metodu — route yoxdur, yalnız daxildə istifadə olunur.
     */
    private function getFinApiToken(): ?string
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

        // ✅ Token artıq "data.accessToken" içindədir
        return $response->json('data.accessToken')
            ?? $response->json('accessToken')
            ?? $response->json('token')
            ?? $response->json('data.token')
            ?? null;
    }
}
