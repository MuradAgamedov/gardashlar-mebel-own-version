<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymentLoginHelper
{
    /**
     * Sistemə login olub Bearer token qaytarır
     *
     * @return string|null
     */
    public static function getToken()
    {
        try {
            $baseUrl  = env("UNITED_PAYMENT_API_URL", "https://test-vpos.unitedpayment.az/api");
            $email    = env("UNITED_PAYMENT_API_EMAIL", "support@unitedpayment.com");
            $password = env("UNITED_PAYMENT_API_PASSWORD", "Testmerchant12!");

            $response = Http::post($baseUrl . "/auth/", [
                "email"    => $email,
                "password" => $password,
            ]);

            if ($response->successful()) {
                $data  = $response->json();
                return $data["token"] ?? null;
            }

            Log::error("UnitedPayment Login Error: " . $response->body());
            return null;

        } catch (\Exception $e) {
            Log::error("UnitedPayment Exception: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Avtomatik olaraq token ilə sorğu göndərmək üçün
     */
    public static function withAuth()
    {
        $token   = self::getToken();
        $baseUrl = env("UNITED_PAYMENT_API_URL", "https://test-vpos.unitedpayment.az/api");

        return Http::withHeaders([
            "x-auth-token" => $token,
        ])->baseUrl($baseUrl);
    }
}
