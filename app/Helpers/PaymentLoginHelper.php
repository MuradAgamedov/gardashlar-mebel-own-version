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
    
            
    
            $response = Http::withOptions([
                'verify' => false,
                'version' => 1.1,
            ])->withHeaders([
                'User-Agent' => 'curl/7.88.1',
                'Content-Type' => 'application/json; charset=utf-8',
            ])->post('https://vpos.unitedpayment.az/api/auth/', [
                'email' => 'office@gardashlar.com',
                'password' => '2&nOl0J9^O-#',
            ]);
            
            dd($response->status(), $response->body());
            
            dd($response);
            Log::info('UnitedPayment Login raw response:', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);
    
            if ($response->successful()) {
                $data = $response->json();
                $token = $data['token'] ?? null;
    
                if ($token) {
                    Cache::put('united_payment_token', $token, now()->addMinutes(55));
                    Log::info('✅ UnitedPayment token alındı.');
                    return $token;
                }
            }
    
            Log::error('❌ UnitedPayment login uğursuz', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);
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
