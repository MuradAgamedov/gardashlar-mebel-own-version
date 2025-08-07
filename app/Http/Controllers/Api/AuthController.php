<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\EmailService;
use App\Services\PhoneService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 * Authentication Controller
 * 
 * @OA\Server(
 *     url="http://127.0.0.1:8000/api",
 *     description="Local API Server"
 * )
 * 
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 */

class AuthController extends Controller
{
    protected $emailService;
    protected $phoneService;

    public function __construct(EmailService $emailService, PhoneService $phoneService)
    {
        $this->emailService = $emailService;
        $this->phoneService = $phoneService;
    }

    /**
     * Register a new user
     * 
     * @OA\Post(
     *     path="/register",
     *     operationId="registerUser",
     *     tags={"Authentication"},
     *     summary="Register a new user",
     *     description="Register a new user with email or phone number and send OTP for verification",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"full_name","password","password_confirmation"},
     *             @OA\Property(property="full_name", type="string", example="John Doe", description="Full name of the user"),
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com", description="Email address (optional if phone provided)"),
     *             @OA\Property(property="phone", type="string", example="0504194230", description="Phone number (optional if email provided)"),
     *             @OA\Property(property="password", type="string", example="password123", description="Password (minimum 6 characters)"),
     *             @OA\Property(property="password_confirmation", type="string", example="password123", description="Password confirmation")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User registered successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="İstifadəçi uğurla qeydiyyatdan keçdi və OTP kodu göndərildi"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="user",
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="full_name", type="string", example="John Doe"),
     *                     @OA\Property(property="phone", type="string", example="0504194230"),
     *                     @OA\Property(property="email", type="string", example="user@example.com")
     *                 ),
     *                 @OA\Property(property="otp_sent", type="boolean", example=true),
     *                 @OA\Property(property="contact_type", type="string", example="email"),
     *                 @OA\Property(property="contact_info", type="string", example="user@example.com")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validation failed"),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(property="full_name", type="array", @OA\Items(type="string")),
     *                 @OA\Property(property="email", type="array", @OA\Items(type="string")),
     *                 @OA\Property(property="password", type="array", @OA\Items(type="string"))
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="OTP kodu göndərilə bilmədi"),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(property="otp", type="array", @OA\Items(type="string"))
     *             )
     *         )
     *     )
     * )
     */
    public function register(Request $request)
    {
        // Debug: Log incoming request data
        \Log::info('Registration request received', [
            'data' => $request->all(),
            'headers' => $request->headers->all()
        ]);

        $validator = Validator::make($request->all(), [
            'full_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'password_confirmation' => 'required|string|min:6',
        ], [
            'full_name.required' => 'Ad və soyad məcburidir',
            'phone.nullable' => 'Telefon nömrəsi seçimlidir',
            'email.nullable' => 'Email ünvanı seçimlidir',
            'email.email' => 'Düzgün email ünvanı daxil edin',
            'email.unique' => 'Bu email ünvanı artıq istifadə olunub',
            'password.required' => 'Şifrə məcburidir',
            'password.min' => 'Şifrə ən azı 6 simvol olmalıdır',
            'password.confirmed' => 'Şifrə təkrarı düzgün deyil',
            'password_confirmation.required' => 'Şifrə təkrarı məcburidir',
        ]);

        // Custom validation: either phone or email must be provided
        $validator->after(function ($validator) use ($request) {
            if (empty($request->phone) && empty($request->email)) {
                $validator->errors()->add('contact_info', 'Telefon nömrəsi və ya email ünvanından ən azı biri məcburidir');
            }
        });

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Create user first
            $user = User::create([
                'full_name' => $request->full_name,
                'phone' => $request->phone,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            // Send OTP automatically after registration
            $otpSent = false;
            $contactType = '';
            $contactInfo = '';

            if (!empty($request->email)) {
                $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
                try {
                    // Store OTP in database
                    \App\Models\OtpCode::create([
                        'contact_info' => $request->email,
                        'contact_type' => 'email',
                        'otp_code' => $otp,
                        'expires_at' => \Carbon\Carbon::now()->addMinutes(10),
                        'is_used' => false
                    ]);
                    
                    // Send email directly like TestEmail command
                    \Illuminate\Support\Facades\Mail::to($request->email)->send(new \App\Mail\OTPMail($otp));
                    $otpSent = true;
                    $contactType = 'email';
                    $contactInfo = $request->email;
                } catch (\Exception $e) {
                    $otpSent = false;
                }
            } elseif (!empty($request->phone)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Telefon nömrəsi ilə qeydiyyat hələ mövcud deyil',
                    'errors' => [
                        'phone' => ['Telefon nömrəsi ilə qeydiyyat hələ mövcud deyil']
                    ]
                ], 422);
            }

            // If OTP sending failed, delete the user and return error
            if (!$otpSent) {
                $user->delete(); // Delete the created user
                return response()->json([
                    'success' => false,
                    'message' => 'OTP kodu göndərilə bilmədi. Zəhmət olmasa yenidən cəhd edin.',
                    'errors' => [
                        'otp' => ['OTP kodu göndərilə bilmədi']
                    ]
                ], 500);
            }

            $response = [
                'success' => true,
                'message' => 'İstifadəçi uğurla qeydiyyatdan keçdi və OTP kodu göndərildi',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'full_name' => $user->full_name,
                        'phone' => $user->phone,
                        'email' => $user->email,
                    ],
                    'otp_sent' => true,
                    'contact_type' => $contactType,
                    'contact_info' => $contactInfo
                ]
            ];

            return response()->json($response, 201);

        } catch (\Exception $e) {
            \Log::error('Registration error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Qeydiyyat zamanı xəta baş verdi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send OTP for registration
     */
    public function sendOTP(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email',
        ], [
            'phone.nullable' => 'Telefon nömrəsi seçimlidir',
            'email.nullable' => 'Email ünvanı seçimlidir',
            'email.email' => 'Düzgün email ünvanı daxil edin',
        ]);

        // Custom validation: either phone or email must be provided
        $validator->after(function ($validator) use ($request) {
            if (empty($request->phone) && empty($request->email)) {
                $validator->errors()->add('contact_info', 'Telefon nömrəsi və ya email ünvanından ən azı biri məcburidir');
            }
        });

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            if (!empty($request->email)) {
                $otp = $this->emailService->generateOTP();
                $sent = $this->emailService->sendOTP($request->email, $otp);
                
                if ($sent) {
                    return response()->json([
                        'success' => true,
                        'message' => 'OTP kodu email ünvanına göndərildi',
                        'data' => [
                            'contact_type' => 'email',
                            'contact_info' => $request->email
                        ]
                    ]);
                }
            } elseif (!empty($request->phone)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Telefon nömrəsi ilə OTP göndərilməsi hələ mövcud deyil',
                    'errors' => [
                        'phone' => ['Telefon nömrəsi ilə OTP göndərilməsi hələ mövcud deyil']
                    ]
                ], 422);
            }

            return response()->json([
                'success' => false,
                'message' => 'OTP kodu göndərilə bilmədi'
            ], 500);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'OTP göndərilməsi zamanı xəta baş verdi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Login user
     * 
     * @OA\Post(
     *     path="/login",
     *     operationId="loginUser",
     *     tags={"Authentication"},
     *     summary="Login user",
     *     description="Login user with email/phone and password. Only verified users can login.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"password"},
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com", description="Email address (optional if phone provided)"),
     *             @OA\Property(property="phone", type="string", example="0504194230", description="Phone number (optional if email provided)"),
     *             @OA\Property(property="password", type="string", example="password123", description="Password")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Uğurla giriş edildi"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="user",
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="full_name", type="string", example="John Doe"),
     *                     @OA\Property(property="phone", type="string", example="0504194230"),
     *                     @OA\Property(property="email", type="string", example="user@example.com")
     *                 ),
     *                 @OA\Property(property="token", type="string", example="1|abc123..."),
     *                 @OA\Property(property="token_type", type="string", example="Bearer")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Invalid credentials",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Email və ya şifrə düzgün deyil"),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(property="credentials", type="array", @OA\Items(type="string"))
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Account not verified",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Hesabınız hələ təsdiqlənməyib"),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(property="verification", type="array", @OA\Items(type="string"))
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validation failed"),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(property="email", type="array", @OA\Items(type="string")),
     *                 @OA\Property(property="password", type="array", @OA\Items(type="string"))
     *             )
     *         )
     *     )
     * )
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string',
        ], [
            'email.nullable' => 'Email ünvanı seçimlidir',
            'email.email' => 'Düzgün email ünvanı daxil edin',
            'phone.nullable' => 'Telefon nömrəsi seçimlidir',
            'password.required' => 'Şifrə məcburidir',
        ]);

        // Custom validation: either phone or email must be provided
        $validator->after(function ($validator) use ($request) {
            if (empty($request->phone) && empty($request->email)) {
                $validator->errors()->add('contact_info', 'Telefon nömrəsi və ya email ünvanından ən azı biri məcburidir');
            }
        });

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Find user by email or phone
            $user = null;
            if (!empty($request->email)) {
                $user = User::where('email', $request->email)->first();
            } elseif (!empty($request->phone)) {
                $user = User::where('phone', $request->phone)->first();
            }

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'İstifadəçi tapılmadı',
                    'errors' => [
                        'credentials' => ['Email və ya şifrə düzgün deyil']
                    ]
                ], 401);
            }

            // Check if user is verified (has verified OTP)
            $isVerified = false;
            if (!empty($request->email)) {
                $isVerified = OtpCode::where('contact_info', $request->email)
                    ->where('contact_type', 'email')
                    ->where('is_used', true)
                    ->exists();
            } elseif (!empty($request->phone)) {
                $isVerified = OtpCode::where('contact_info', $request->phone)
                    ->where('contact_type', 'phone')
                    ->where('is_used', true)
                    ->exists();
            }

            if (!$isVerified) {
                return response()->json([
                    'success' => false,
                    'message' => 'Hesabınız hələ təsdiqlənməyib',
                    'errors' => [
                        'verification' => ['Zəhmət olmasa əvvəlcə hesabınızı təsdiqləyin']
                    ]
                ], 403);
            }

            // Check password
            if (!Hash::check($request->password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email və ya şifrə düzgün deyil',
                    'errors' => [
                        'credentials' => ['Email və ya şifrə düzgün deyil']
                    ]
                ], 401);
            }

            // Generate token
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Uğurla giriş edildi',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'full_name' => $user->full_name,
                        'phone' => $user->phone,
                        'email' => $user->email,
                    ],
                    'token' => $token,
                    'token_type' => 'Bearer'
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Login error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Giriş zamanı xəta baş verdi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Logout user
     * 
     * @OA\Post(
     *     path="/logout",
     *     operationId="logoutUser",
     *     tags={"Authentication"},
     *     summary="Logout user",
     *     description="Logout user and invalidate current token",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Logout successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Uğurla çıxış edildi")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     )
     * )
     */
    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'success' => true,
                'message' => 'Uğurla çıxış edildi'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Çıxış zamanı xəta baş verdi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verify OTP
     * 
     * @OA\Post(
     *     path="/verify-otp",
     *     operationId="verifyOTP",
     *     tags={"Authentication"},
     *     summary="Verify OTP code",
     *     description="Verify OTP code sent to email or phone number",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"otp_code"},
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com", description="Email address (optional if phone provided)"),
     *             @OA\Property(property="phone", type="string", example="0504194230", description="Phone number (optional if email provided)"),
     *             @OA\Property(property="otp_code", type="string", example="123456", description="6-digit OTP code")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OTP verified successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="OTP kodu uğurla təsdiqləndi"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="contact_type", type="string", example="email"),
     *                 @OA\Property(property="contact_info", type="string", example="user@example.com"),
     *                 @OA\Property(property="verified", type="boolean", example=true)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Invalid OTP or validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="OTP kodu düzgün deyil və ya vaxtı keçib"),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(property="otp_code", type="array", @OA\Items(type="string"))
     *             )
     *         )
     *     )
     * )
     */
    public function verifyOTP(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'otp_code' => 'required|string|size:6',
        ], [
            'phone.nullable' => 'Telefon nömrəsi seçimlidir',
            'email.nullable' => 'Email ünvanı seçimlidir',
            'email.email' => 'Düzgün email ünvanı daxil edin',
            'otp_code.required' => 'OTP kodu məcburidir',
            'otp_code.size' => 'OTP kodu 6 simvol olmalıdır',
        ]);

        // Custom validation: either phone or email must be provided
        $validator->after(function ($validator) use ($request) {
            if (empty($request->phone) && empty($request->email)) {
                $validator->errors()->add('contact_info', 'Telefon nömrəsi və ya email ünvanından ən azı biri məcburidir');
            }
        });

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $otpVerified = false;
            $contactType = '';
            $contactInfo = '';

            if (!empty($request->email)) {
                $otpVerified = $this->emailService->verifyOTP($request->email, $request->otp_code);
                $contactType = 'email';
                $contactInfo = $request->email;
            } elseif (!empty($request->phone)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Telefon nömrəsi ilə OTP təsdiqləməsi hələ mövcud deyil',
                    'errors' => [
                        'phone' => ['Telefon nömrəsi ilə OTP təsdiqləməsi hələ mövcud deyil']
                    ]
                ], 422);
            }

            if ($otpVerified) {
                return response()->json([
                    'success' => true,
                    'message' => 'OTP kodu uğurla təsdiqləndi',
                    'data' => [
                        'contact_type' => $contactType,
                        'contact_info' => $contactInfo,
                        'verified' => true
                    ]
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'OTP kodu düzgün deyil və ya vaxtı keçib',
                    'errors' => [
                        'otp_code' => ['OTP kodu düzgün deyil və ya vaxtı keçib']
                    ]
                ], 422);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'OTP təsdiqləməsi zamanı xəta baş verdi',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
