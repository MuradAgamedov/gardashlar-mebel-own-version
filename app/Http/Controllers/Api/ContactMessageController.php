<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ContactMessage\StoreContactMessageRequest;
use App\Models\ContactMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use OpenApi\Annotations as OA;


class ContactMessageController extends Controller
{
     
    public function store(StoreContactMessageRequest $request): JsonResponse
    {
        try {
            // Verify reCAPTCHA token
            $recaptchaResponse = $this->verifyRecaptcha($request->recaptcha_token);
            if (!$recaptchaResponse['success']) {
                return response()->json([
                    'success' => false,
                    'message' => dict('recaptcha_verification_failed'),
                    'errors' => ['recaptcha' => [dict('recaptcha_verification_failed')]]
                ], 422);
            }

            $message = ContactMessage::create([
                'name' => $request->name,
                'email' => $request->email,
                'city' => $request->city,
                'message' => $request->message,
                'is_read' => false
            ]);

            return response()->json([
                'success' => true,
                'message' => dict('message_sent_successfully'),
                'data' => $message
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => dict('message_send_error') . ': ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    /**
     * Verify reCAPTCHA token
     */
    private function verifyRecaptcha(string $token): array
    {
        try {
            $secretKey = env('SECRET_KEY');
            $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => $secretKey,
                'response' => $token
            ]);

            return $response->json();
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

  
    public function getRecaptchaSiteKey(): JsonResponse
    {
        try {
            $siteKey = env('SITE_KEY');
            
            if (!$siteKey) {
                return response()->json([
                    'success' => false,
                    'message' => 'reCAPTCHA site key not configured'
                ], 500);
            }

            return response()->json([
                'success' => true,
                'site_key' => $siteKey
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving site key: ' . $e->getMessage()
            ], 500);
        }
    }
}
