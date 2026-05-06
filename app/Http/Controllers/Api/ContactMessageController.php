<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="Contact Messages",
 *     description="API Endpoints for Contact Messages"
 * )
 */
class ContactMessageController extends Controller
{
        /**
     * @OA\Post(
     *     path="/api/contact-messages",
     *     operationId="storeContactMessage",
     *     tags={"Contact Messages"},
     *     summary="Send contact message",
     *     description="Send a new contact message from the contact form",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","email","city","message","recaptcha_token"},
     *             @OA\Property(property="name", type="string", example="John Doe", description="Full name of the sender"),
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com", description="Email address of the sender"),
     *             @OA\Property(property="city", type="string", example="Baku", description="City of the sender"),
     *             @OA\Property(property="message", type="string", example="Hello, I have a question about your products...", description="The message content"),
     *             @OA\Property(property="recaptcha_token", type="string", example="03AOLTBLR5...", description="reCAPTCHA token from frontend")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Contact message sent successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Mesajınız uğurla göndərildi"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="John Doe"),
     *                 @OA\Property(property="email", type="string", example="john@example.com"),
     *                 @OA\Property(property="city", type="string", example="Baku"),
     *                 @OA\Property(property="message", type="string", example="Hello, I have a question..."),
     *                 @OA\Property(property="is_read", type="boolean", example=false),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-08-05T10:30:00.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-08-05T10:30:00.000000Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validation failed"),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(
     *                     property="name",
     *                     type="array",
     *                     @OA\Items(type="string", example="Ad və soyad tələb olunur")
     *                 ),
     *                 @OA\Property(
     *                     property="email",
     *                     type="array",
     *                     @OA\Items(type="string", example="Düzgün email ünvanı daxil edin")
     *                 ),
     *                 @OA\Property(
     *                     property="city",
     *                     type="array",
     *                     @OA\Items(type="string", example="Şəhər tələb olunur")
     *                 ),
     *                 @OA\Property(
     *                     property="message",
     *                     type="array",
     *                     @OA\Items(type="string", example="Mesaj tələb olunur")
     *                 ),
     *                 @OA\Property(
     *                     property="recaptcha_token",
     *                     type="array",
     *                     @OA\Items(type="string", example="reCAPTCHA tələb olunur")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Mesaj göndərilərkən xəta baş verdi"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     )
     * )
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'city' => 'required|string|max:255',
                'message' => 'required|string|max:5000',
                'recaptcha_token' => 'required|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Verify reCAPTCHA token
            $recaptchaResponse = $this->verifyRecaptcha($request->recaptcha_token);
            if (!$recaptchaResponse['success']) {
                return response()->json([
                    'success' => false,
                    'message' => 'reCAPTCHA verification failed',
                    'errors' => ['recaptcha' => ['reCAPTCHA təsdiqlənmədi']]
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
                'message' => 'Mesajınız uğurla göndərildi',
                'data' => $message
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Mesaj göndərilərkən xəta baş verdi: ' . $e->getMessage(),
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

    /**
     * Get reCAPTCHA site key
     * 
     * @OA\Get(
     *     path="/api/recaptcha/site-key",
     *     operationId="getRecaptchaSiteKey",
     *     tags={"Contact Messages"},
     *     summary="Get reCAPTCHA site key",
     *     description="Returns the reCAPTCHA site key for frontend integration",
     *     @OA\Response(
     *         response=200,
     *         description="Site key retrieved successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="site_key", type="string", example="6LeIxAcTAAAAAJcZVRqyHh71UMIEbUjQ...")
     *         )
     *     )
     * )
     */
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
