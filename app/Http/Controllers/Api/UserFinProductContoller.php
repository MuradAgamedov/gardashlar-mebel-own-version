<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class  UserFinProductContoller extends Controller
{
    public function index(Request $request)
    {
        // 1️⃣ FIN kodunu request-dən al
        $fin = $request->input('fin');
        if (! $fin) {
            return response()->json(['error' => 'fin is required'], 422);
        }

        // 2️⃣ ENV məlumatlarını oxu
        $baseUrl = rtrim(env('FIN_API_BASE_URL'), '/');
        $email = env('FIN_API_EMAIL');
        $password = env('FIN_API_PASSWORD');

        if (! $baseUrl || ! $email || ! $password) {
            return response()->json(['error' => 'API credentials missing in .env'], 500);
        }

        try {
            // 3️⃣ Əvvəlcə login et və token al
            $loginResponse = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post("{$baseUrl}/login", [
                'email' => $email,
                'password' => $password,
                'deviceToken' => '',
            ]);

            if ($loginResponse->failed()) {
                return response()->json([
                    'error' => 'Login failed',
                    'details' => $loginResponse->json(),
                ], $loginResponse->status());
            }

            // Tokeni tap
            $token = $loginResponse->json('data.token')
                ?? $loginResponse->json('token')
                ?? null;

            if (! $token) {
                return response()->json(['error' => 'Token not found in login response'], 500);
            }

            // 4️⃣ Tokenlə fin-ə əsasən məlumat al
            $invoiceUrl = "{$baseUrl}/sales/invoice-products?fin={$fin}";

            $invoiceResponse = Http::withToken($token)
                ->acceptJson()
                ->get($invoiceUrl);

            // 5️⃣ Əgər uğurludursa tam cavabı qaytar
            if ($invoiceResponse->successful()) {
                return response()->json($invoiceResponse->json());
            }

            // 6️⃣ Uğursuzdursa statusla birlikdə cavabı qaytar
            return response()->json([
                'error' => 'Invoice API request failed',
                'details' => $invoiceResponse->json(),
            ], $invoiceResponse->status());

        } catch (\Throwable $e) {
            return response()->json([
                'error' => 'Unexpected error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
