<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class UserFinProductContoller extends Controller
{
    public function index(Request $request)
    {
        // 1️⃣ FIN kodunu request-dən al
        $fin = $request->input('fin');
        if (!$fin) {
            return response()->json(['error' => 'fin is required'], 422);
        }

        // 2️⃣ ENV məlumatlarını oxu
        $baseUrl = rtrim(env('FIN_API_BASE_URL'), '/');
        $email = env('FIN_API_EMAIL');
        $password = env('FIN_API_PASSWORD');

        if (!$baseUrl || !$email || !$password) {
            return response()->json(['error' => 'API credentials missing in .env'], 500);
        }

        try {
            // 3️⃣ Login üçün API-yə POST göndər
            $loginUrl = "{$baseUrl}/login";

            $loginResponse = Http::asJson()->post($loginUrl, [
                'email' => $email,
                'password' => $password,
                'deviceToken' => '',
            ]);

            if ($loginResponse->failed()) {
                return response()->json([
                    'error' => 'Login request failed',
                    'login_url' => $loginUrl,
                    'details' => $loginResponse->json(),
                ], $loginResponse->status());
            }

            // 4️⃣ Token tap
            $token = $loginResponse->json('data.token')
                ?? $loginResponse->json('token')
                ?? null;

            if (!$token) {
                return response()->json([
                    'error' => 'Token not found in login response',
                    'response' => $loginResponse->json(),
                ], 500);
            }

            // 5️⃣ Token ilə fin sorğusu et
            $invoiceUrl = "{$baseUrl}/sales/invoice-products?fin=" . urlencode($fin);

            $invoiceResponse = Http::withToken($token)
                ->acceptJson()
                ->get($invoiceUrl);

            // 6️⃣ Əgər uğurludursa tam cavabı qaytar
            if ($invoiceResponse->successful()) {
                return response()->json($invoiceResponse->json(), 200);
            }

            // 7️⃣ Əks halda xətanı qaytar
            return response()->json([
                'error' => 'Invoice API request failed',
                'url' => $invoiceUrl,
                'details' => $invoiceResponse->json(),
            ], $invoiceResponse->status());

        } catch (\Throwable $e) {
            return response()->json([
                'error' => 'Unexpected error occurred',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
