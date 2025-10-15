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
            $baseUrl  = env("UNITED_PAYMENT_API_URL", "https://vpos.unitedpayment.az/api");
            $email    = env("UNITED_PAYMENT_API_EMAIL", "office@gardashlar.com");
            $password = env("UNITED_PAYMENT_API_PASSWORD", "2&nOl0J9^O-#");
    
            // Cache-də varsa, 55 dəqiqəlik istifadə et
            if (Cache::has('united_payment_token')) {
                return Cache::get('united_payment_token');
            }
    
            // === CURL ilə login sorğusu ===
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => $baseUrl . "/auth/",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_HTTPHEADER => [
                    "Content-Type: application/json; charset=utf-8",
                ],
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => json_encode([
                    "email"    => $email,
                    "password" => $password
                ], JSON_UNESCAPED_UNICODE),
            ]);
    
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
            if (curl_errno($ch)) {
                Log::error("❌ CURL Error: " . curl_error($ch));
                curl_close($ch);
                return null;
            }
    
            curl_close($ch);
    
            if ($httpCode !== 200) {
                Log::error("❌ UnitedPayment Login failed", [
                    'http_code' => $httpCode,
                    'response'  => $response,
                ]);
                return null;
            }
    
            $data = json_decode($response, true);
    
            if (isset($data['token'])) {
                $token = $data['token'];
                Cache::put('united_payment_token', $token, now()->addMinutes(55));
                Log::info("✅ UnitedPayment token uğurla alındı.");
                return $token;
            } else {
                Log::error("⚠️ Token tapılmadı. Cavab: " . $response);
                return null;
            }
    
        } catch (\Exception $e) {
            Log::error("UnitedPayment Exception: " . $e->getMessage());
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
