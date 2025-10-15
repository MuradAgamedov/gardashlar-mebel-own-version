<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class PaymentLoginHelper
{
    /**
     * Sistemə login olub Bearer token qaytarır
     */
    public static function getToken()
    {
        try {
            // .env-dən gələn parametrlər
            $baseUrl  = env("UNITED_PAYMENT_API_URL", "https://vpos.unitedpayment.az/api");
            $email    = env("UNITED_PAYMENT_API_EMAIL", "office@gardashlar.com");
            $password = env("UNITED_PAYMENT_API_PASSWORD", "2&nOl0J9^O-#");

            // Cache-də varsa, 55 dəqiqəlik istifadə et
            if (Cache::has('united_payment_token')) {
                return Cache::get('united_payment_token');
            }

            $response = Http::withHeaders([
                'Content-Type' => 'application/json; charset=utf-8'
            ])->post($baseUrl . '/auth/', [
                'email'    => $email,
                'password' => $password,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $token = $data['token'] ?? null;

                if ($token) {
                    Cache::put('united_payment_token', $token, now()->addMinutes(55));
                    Log::info('✅ UnitedPayment token yeniləndi.');
                    return $token;
                }
            }

            Log::error('❌ UnitedPayment login xətası: ' . $response->status() . ' | ' . $response->body());
            return null;

        } catch (\Exception $e) {
            Log::error('UnitedPayment Exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Token ilə sorğu göndərmək üçün wrapper
     */
    public static function withAuth()
{
    $token   = self::getToken();
    dd($token);
    $baseUrl = env("UNITED_PAYMENT_API_URL", "https://vpos.unitedpayment.az/api");

    // Bearer sözü varsa, onu təmizləyək (dəfələrlə Bearer yazılmasın)
    $token = str_replace('Bearer ', '', $token);

    return Http::withHeaders([
        'Authorization' => 'Bearer ' . $token, // 👈 Dəyişiklik budur
        'Content-Type'  => 'application/json; charset=utf-8'
    ])->baseUrl($baseUrl);
}

}
