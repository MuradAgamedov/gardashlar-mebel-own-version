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

use App\Models\Dictionary;
use App\Models\Language;
class AuthController extends Controller
{
    protected $emailService;
    protected $phoneService;

    public function __construct(EmailService $emailService, PhoneService $phoneService)
    {
        $this->emailService = $emailService;
        $this->phoneService = $phoneService;
    }


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
            'fin' => ['nullable', 'regex:/^[A-NP-Z0-9]{7}$/'],
        ], [
            'full_name.required' => 'Ad və soyad məcburidir',
            'phone.nullable' => 'Telefon nömrəsi seçimlidir',
            'email.nullable' => 'Email ünvanı seçimlidir',
            'email.email' => 'Düzgün email ünvanı daxil edin',
            'email.unique' => 'user_already_exists',
            'password.required' => 'Şifrə məcburidir',
            'password.min' => 'Şifrə ən azı 6 simvol olmalıdır',
            'password.confirmed' => 'Şifrə təkrarı düzgün deyil',
            'password_confirmation.required' => 'Şifrə təkrarı məcburidir',
            'fin.regex' => 'FIN yalnız 7 simvoldan ibarət olmalı və A-N, P-Z və 0-9 aralığında olmalıdır',
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
                "fin" => $request->fin,
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
                        'is_used' => false,
                        'purpose' => 'verification'
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
                        'fin' => $user->fin,
                    ],
                    'otp_sent' => true,
                    'contact_type' => $contactType,
                    'contact_info' => $contactInfo,
                    'expires_at' => now()->addMinutes(10)->toISOString()
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
        app()->setLocale('en');    
        try {
            if (!empty($request->email)) {
                $sent = $this->emailService->sendOTP($request->email, 'verification');

                if ($sent) {
                    return response()->json([
                        'success' => true,
                        'message' => 'OTP kodu email ünvanına göndərildi',
                        'data' => [
                            'contact_type' => 'email',
                            'contact_info' => $request->email,
                            'expires_at' => now()->addMinutes(10)->toISOString()
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


    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string',
        ], [
            'email.nullable' => Dictionary::getValue('email_nullable'),
            'email.email' => Dictionary::getValue('email_email'),
            'phone.nullable' => Dictionary::getValue('phone_nullable'),
            'password.required' => Dictionary::getValue('password_required'),
        ]);

        // Custom validation: either phone or email must be provided
        $validator->after(function ($validator) use ($request) {
            if (empty($request->phone) && empty($request->email)) {
                $validator->errors()->add('contact_info', __('Telefon nömrəsi və ya email ünvanından ən azı biri məcburidir'));
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
                    'message' => Dictionary::getValue('user_not_found'),
                    'errors' => [
                        'credentials' => [Dictionary::getValue('invalid_credentials'),]
                    ]
                ], 401);
            }

            // Check if user email is verified
            \Log::info('Login verification check', [
                'user_id' => $user->id,
                'email' => $user->email,
                'email_verified_at' => $user->email_verified_at,
                'is_null_check' => is_null($user->email_verified_at)
            ]);

            if (is_null($user->email_verified_at)) {
                return response()->json([
                    'success' => false,
                    'message' => Dictionary::getValue('account_not_verified'),
                    'errors' => [
                        'verification' => [Dictionary::getValue('verify_account')]
                    ]
                ], 403);
            }

            // Check password
            if (!Hash::check($request->password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => Dictionary::getValue('invalid_credentials'),
                    'errors' => [
                        'credentials' => [Dictionary::getValue('invalid_credentials')]
                    ]
                ], 401);
            }

            // Generate token
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => Dictionary::getValue('success_login'),
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'surname' => $user->surname,
                        'phone' => $user->phone,
                        'email' => $user->email,
                        'fin' => $user->fin,
                        'email_verified' => !is_null($user->email_verified_at),
                        'email_verified_at' => $user->email_verified_at ? $user->email_verified_at->toDateTimeString() : null
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
                'message' => Dictionary::getValue('login_error', $request->header("locale")),
                'error' => $e->getMessage()
            ], 500);
        }
    }

   
    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'success' => true,
                'message' => Dictionary::getValue('success_logout')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => Dictionary::getValue('logout_error'),
                'error' => $e->getMessage()
            ], 500);
        }
    }

   
    public function resendOTP(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'purpose' => 'nullable|string|in:registration,password_reset',
        ], [
            'phone.nullable' => Dictionary::getValue('phone_nullable'),
            'email.nullable' => Dictionary::getValue('email_nullable'),
            'email.email' => Dictionary::getValue('email_email'),
            'purpose.in' => Dictionary::getValue('purpose_in'),
        ]);

        // Custom validation: either phone or email must be provided
        $validator->after(function ($validator) use ($request) {
            if (empty($request->phone) && empty($request->email)) {
                $validator->errors()->add('contact_info', Dictionary::getValue('contact_info'));
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
            $contactInfo = !empty($request->email) ? $request->email : $request->phone;
            $contactType = !empty($request->email) ? 'email' : 'phone';
            $purpose = $request->purpose ?? 'registration';

            // Rate limiting: Check if OTP was sent recently (within 1 minute)
            $recentOtp = \App\Models\OtpCode::where('contact_info', $contactInfo)
                ->where('contact_type', $contactType)
                ->where('created_at', '>', \Carbon\Carbon::now()->subMinute())
                ->first();

            if ($recentOtp) {
                return response()->json([
                    'success' => false,
                    'message' => Dictionary::getValue('rate_limit'),
                    'errors' => [
                        'rate_limit' => [Dictionary::getValue('rate_limit')]
                    ]
                ], 429);
            }

            $otpSent = false;

            if (!empty($request->email)) {
                $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
                try {
                    // Store OTP in database
                    \App\Models\OtpCode::create([
                        'contact_info' => $request->email,
                        'contact_type' => 'email',
                        'otp_code' => $otp,
                        'expires_at' => \Carbon\Carbon::now()->addMinutes(10),
                        'is_used' => false,
                        'purpose' => $purpose
                    ]);

                    // Send email with appropriate subject
                    $subject = $purpose === 'password_reset' ? 'Şifrə Yeniləmə' : 'OTP Kodu';
                    \Illuminate\Support\Facades\Mail::to($request->email)->send(new \App\Mail\OTPMail($otp, $subject));
                    $otpSent = true;

                    \Log::info('OTP resent successfully', [
                        'email' => $request->email,
                        'otp' => $otp
                    ]);
                } catch (\Exception $e) {
                    \Log::error('Failed to resend OTP via email', [
                        'email' => $request->email,
                        'error' => $e->getMessage()
                    ]);
                    $otpSent = false;
                }
            } elseif (!empty($request->phone)) {
                return response()->json([
                    'success' => false,
                    'message' => Dictionary::getValue('otp_not_available'),
                    'errors' => [
                        'phone' => [Dictionary::getValue('otp_not_available')]
                    ]
                ], 422);
            }

            if ($otpSent) {
                return response()->json([
                    'success' => true,
                    'message' => Dictionary::getValue('otp_sent'),
                    'data' => [
                        'contact_type' => $contactType,
                        'contact_info' => $contactInfo,
                        'otp_sent' => true,
                        'expires_at' => now()->addMinutes(10)->toISOString()
                    ]
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => Dictionary::getValue('otp_not_sent'),
                ], 500);
            }

        } catch (\Exception $e) {
            \Log::error('Resend OTP error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => Dictionary::getValue('otp_not_sent'),
                'error' => $e->getMessage()
            ], 500);
        }
    }

  
    public function verifyOTP(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'otp_code' => 'required|string|size:6',
        ], [
            'phone.nullable' => Dictionary::getValue('phone_nullable'),
            'email.nullable' => Dictionary::getValue('email_nullable'),
            'email.email' => Dictionary::getValue('email_email'),
            'otp_code.required' => Dictionary::getValue('otp_code_required'),
            'otp_code.size' => Dictionary::getValue('otp_code_size'),
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
                $otpVerified = $this->emailService->verifyOTP($request->email, $request->otp_code, 'registration');
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

   
    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ], [
            'email.required' => 'Email ünvanı məcburidir',
            'email.email' => 'Düzgün email ünvanı daxil edin',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Check if user exists
            $user = User::where('email', $request->email)->first();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bu email ünvanı ilə istifadəçi tapılmadı',
                    'errors' => [
                        'email' => ['Bu email ünvanı ilə qeydiyyatdan keçmiş istifadəçi yoxdur']
                    ]
                ], 404);
            }

            // Rate limiting: Check if OTP was sent recently (within 1 minute)
            $recentOtp = \App\Models\OtpCode::where('contact_info', $request->email)
                ->where('contact_type', 'email')
                ->where('created_at', '>', \Carbon\Carbon::now()->subMinute())
                ->first();

            if ($recentOtp) {
                return response()->json([
                    'success' => false,
                    'message' => 'Çox tez-tez cəhd etmisiniz. Zəhmət olmasa 1 dəqiqə gözləyin.',
                    'errors' => [
                        'rate_limit' => ['1 dəqiqə ərzində yalnız bir dəfə OTP göndərə bilərsiniz']
                    ]
                ], 429);
            }

            // Generate and send OTP
            $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

            // Store OTP in database with special purpose
            \App\Models\OtpCode::create([
                'contact_info' => $request->email,
                'contact_type' => 'email',
                'otp_code' => $otp,
                'expires_at' => \Carbon\Carbon::now()->addMinutes(10),
                'is_used' => false,
                'purpose' => 'password_reset' // Special purpose for password reset
            ]);

            // Send email
            \Illuminate\Support\Facades\Mail::to($request->email)->send(new \App\Mail\OTPMail($otp, 'Şifrə Yeniləmə'));

            \Log::info('Password reset OTP sent', [
                'email' => $request->email,
                'otp' => $otp
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Şifrə yeniləmə üçün OTP kodu göndərildi',
                'data' => [
                    'email' => $request->email,
                    'otp_sent' => true,
                    'expires_at' => now()->addMinutes(10)->toISOString()
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Forgot password error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'email' => $request->email
            ]);

            return response()->json([
                'success' => false,
                'message' => 'OTP göndərilməsi zamanı xəta baş verdi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

   
    public function verifyResetOTP(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'otp_code' => 'required|string|size:6',
        ], [
            'email.required' => 'Email ünvanı məcburidir',
            'email.email' => 'Düzgün email ünvanı daxil edin',
            'otp_code.required' => 'OTP kodu məcburidir',
            'otp_code.size' => 'OTP kodu 6 simvol olmalıdır',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Find the OTP record
            $otpRecord = \App\Models\OtpCode::where('contact_info', $request->email)
                ->where('contact_type', 'email')
                ->where('is_used', false)
                ->where('expires_at', '>', \Carbon\Carbon::now())
                ->latest()
                ->first();

            if (!$otpRecord || $otpRecord->otp_code !== $request->otp_code) {
                return response()->json([
                    'success' => false,
                    'message' => 'OTP kodu düzgün deyil və ya vaxtı keçib',
                    'errors' => [
                        'otp_code' => ['OTP kodu düzgün deyil və ya vaxtı keçib']
                    ]
                ], 422);
            }

            // Mark OTP as used
            $otpRecord->markAsUsed();

            // Generate a temporary reset token
            $resetToken = bin2hex(random_bytes(32));

            // Store reset token in cache for 10 minutes
            \Cache::put("password_reset_token_{$resetToken}", $request->email, 600);

            \Log::info('Password reset OTP verified', [
                'email' => $request->email,
                'reset_token' => $resetToken
            ]);

            return response()->json([
                'success' => true,
                'message' => 'OTP təsdiqləndi. İndi yeni şifrə təyin edə bilərsiniz',
                'data' => [
                    'email' => $request->email,
                    'verified' => true,
                    'reset_token' => $resetToken
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Verify reset OTP error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'email' => $request->email
            ]);

            return response()->json([
                'success' => false,
                'message' => 'OTP təsdiqləməsi zamanı xəta baş verdi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

   
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'reset_token' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required|string|min:8',
        ], [
            'reset_token.required' => 'Reset token məcburidir',
            'password.required' => 'Şifrə məcburidir',
            'password.min' => 'Şifrə ən azı 8 simvol olmalıdır',
            'password.confirmed' => 'Şifrə təkrarı düzgün deyil',
            'password_confirmation.required' => 'Şifrə təkrarı məcburidir',
        ]);

        // Additional password validation with regex (like RegisterController)
        $validator->after(function ($validator) use ($request) {
            $password = $request->password;
            if (!empty($password)) {
                if (!preg_match('/[!@#$%^&*()_+\-=\[\]{};\':"\\|,.<>\/?]/', $password)) {
                    $validator->errors()->add('password', 'Şifrədə ən azı bir xüsusi simvol olmalıdır (!@#$%^&* və s.)');
                }
                if (!preg_match('/\d/', $password)) {
                    $validator->errors()->add('password', 'Şifrədə ən azı bir rəqəm olmalıdır');
                }
                if (!preg_match('/[A-Z]/', $password)) {
                    $validator->errors()->add('password', 'Şifrədə ən azı bir böyük hərf olmalıdır');
                }
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
            // Get email from cache using reset token
            $email = \Cache::get("password_reset_token_{$request->reset_token}");

            if (!$email) {
                return response()->json([
                    'success' => false,
                    'message' => 'Reset token düzgün deyil və ya vaxtı keçib',
                    'errors' => [
                        'reset_token' => ['Reset token düzgün deyil və ya vaxtı keçib']
                    ]
                ], 422);
            }

            // Find user
            $user = User::where('email', $email)->first();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'İstifadəçi tapılmadı',
                    'errors' => [
                        'email' => ['Bu email ünvanı ilə istifadəçi tapılmadı']
                    ]
                ], 404);
            }

            // Update password
            $user->password = Hash::make($request->password);

            // BONUS: If email not verified, verify it now
            $emailVerified = false;
            if (is_null($user->email_verified_at)) {
                $user->email_verified_at = now();
                $emailVerified = true;
            }

            $user->save();

            // Remove reset token from cache
            \Cache::forget("password_reset_token_{$request->reset_token}");

            \Log::info('Password reset successfully', [
                'email' => $email,
                'email_verified' => $emailVerified
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Şifrə uğurla yeniləndi' . ($emailVerified ? ' və email təsdiqləndi' : ''),
                'data' => [
                    'email' => $email,
                    'password_reset' => true,
                    'email_verified' => !is_null($user->email_verified_at),
                    'email_verified_now' => $emailVerified
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Reset password error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'reset_token' => $request->reset_token
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Şifrə yeniləməsi zamanı xəta baş verdi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

   
    public function updateProfile(Request $request)
    {
        $locale = request()->header('locale');
        try {
            $user = auth()->user();

            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|required|string|max:255',
                'surname' => 'sometimes|required|string|max:255',
                'phone' => 'sometimes|nullable|string|max:20',
                'email' => 'sometimes|nullable|email|unique:users,email,' . $user->id,
                'fin' => 'nullable|string|unique:users,fin,' . $user->id
            ], [
                'name.required' => 'Ad məcburidir',
                'name.string' => 'Ad mətn formatında olmalıdır',
                'name.max' => 'Ad 255 simvoldan çox ola bilməz',
                'surname.required' => 'Soyad məcburidir',
                'surname.string' => 'Soyad mətn formatında olmalıdır',
                'surname.max' => 'Soyad 255 simvoldan çox ola bilməz',
                'phone.string' => 'Telefon nömrəsi mətn formatında olmalıdır',
                'phone.max' => 'Telefon nömrəsi 20 simvoldan çox ola bilməz',
                'email.email' => 'Düzgün email ünvanı daxil edin',
                'email.unique' => 'user_already_exists',
                'fin.string' => 'FIN mətn formatında olmalıdır',
                'fin.unique' => dict('fin_unique', $locale),
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => dict('validation_error', $locale),
                    'errors' => $validator->errors()
                ], 422);
            }

            $otpSent = false;
            $oldEmail = $user->email;

            // Update basic fields
            if ($request->has('name')) {
                $user->name = $request->name;
            }

            if ($request->has('surname')) {
                $user->surname = $request->surname;
            }

            if ($request->has('phone')) {
                $user->phone = $request->phone;
            }
            if ($request->has('fin')) {
                $user->fin = $request->fin;
            }
            // Handle email change with pending verification
            if ($request->has('email')) {
                if ($request->email !== $oldEmail) {
                    // Email həqiqətən dəyişib
                    if ($request->email) {
                        // Set pending email for verification
                        $user->pending_email = $request->email;
                        $user->pending_email_token = \Str::random(60);
                        $user->pending_email_expires_at = now()->addMinutes(10);

                        $otpSent = true;

                        // Send OTP to new email
                        $this->emailService->sendOTP($request->email, 'email_change');

                        \Log::info('Email change requested', [
                            'user_id' => $user->id,
                            'old_email' => $oldEmail,
                            'new_email' => $request->email
                        ]);
                    } else {
                        // If email is being set to null/empty
                        $user->email = null;
                        $user->email_verified_at = null;
                    }
                }
                // Əgər email eyni qalıbsa, heç bir şey etmirik
            }

            $user->save();

            $responseData = [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'surname' => $user->surname,
                    'phone' => $user->phone,
                    'email' => $user->email,
                    'email_verified_at' => $user->email_verified_at,
                    "fin" => $user->fin
                ],
                'otp_sent' => $otpSent
            ];

            // Add expires_at only if OTP was sent
            if ($otpSent) {
                $responseData['expires_at'] = now()->addMinutes(10)->toISOString();
            }

            return response()->json([
                'success' => true,
                'message' => $otpSent ?
                    'Profil yeniləndi. Yeni email ünvanınıza təsdiq kodu göndərildi' :
                    'Profil uğurla yeniləndi',
                'data' => $responseData
            ]);

        } catch (\Exception $e) {
            \Log::error('Profile update error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Profil yeniləməsi zamanı xəta baş verdi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    
    public function confirmEmailChange(Request $request)
    {
        try {
            $user = auth()->user();

            $validator = Validator::make($request->all(), [
                'otp_code' => 'required|string|size:6',
            ], [
                'otp_code.required' => 'OTP kod məcburidir',
                'otp_code.string' => 'OTP kod mətn formatında olmalıdır',
                'otp_code.size' => 'OTP kod 6 rəqəm olmalıdır',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Check if user has pending email
            if (!$user->pending_email) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email dəyişmə sorğusu tapılmadı',
                    'errors' => [
                        'pending_email' => ['Aktiv email dəyişmə sorğusu yoxdur']
                    ]
                ], 422);
            }

            // Check if pending email token is expired
            if ($user->pending_email_expires_at && $user->pending_email_expires_at->isPast()) {
                // Clear expired pending email
                $user->update([
                    'pending_email' => null,
                    'pending_email_token' => null,
                    'pending_email_expires_at' => null,
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Email dəyişmə sorğusunun vaxtı keçib',
                    'errors' => [
                        'expired' => ['Zəhmət olmasa yenidən cəhd edin']
                    ]
                ], 422);
            }

            // Verify OTP
            $otpValid = $this->emailService->verifyOTP($user->pending_email, $request->otp_code, 'email_change');

            if (!$otpValid) {
                return response()->json([
                    'success' => false,
                    'message' => 'OTP kod düzgün deyil',
                    'errors' => [
                        'otp_code' => ['OTP kod düzgün deyil']
                    ]
                ], 422);
            }

            // Update email and clear pending fields
            $oldEmail = $user->email;
            $user->email = $user->pending_email;
            $user->email_verified_at = now();
            $user->pending_email = null;
            $user->pending_email_token = null;
            $user->pending_email_expires_at = null;
            $user->save();

            \Log::info('Email change confirmed', [
                'user_id' => $user->id,
                'old_email' => $oldEmail,
                'new_email' => $user->email
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Email uğurla təsdiqləndi və dəyişdirildi',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'surname' => $user->surname,
                        'phone' => $user->phone,
                        'email' => $user->email,
                        'fin' => $user->fin,
                        'email_verified_at' => $user->email_verified_at,
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Email confirmation error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Email təsdiqi zamanı xəta baş verdi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

 
    public function resendEmailChangeOTP(Request $request)
    {
        try {
            $user = auth()->user();

            // Check if user has pending email
            if (!$user->pending_email) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aktiv email dəyişmə sorğusu yoxdur',
                    'errors' => [
                        'pending_email' => ['Email dəyişmə sorğusu tapılmadı']
                    ]
                ], 422);
            }

            // Update expiry time
            $user->pending_email_expires_at = now()->addMinutes(10);
            $user->save();

            // Send new OTP
            $otpSent = $this->emailService->sendOTP($user->pending_email, 'email_change');

            if ($otpSent) {
                \Log::info('Email change OTP resent', [
                    'user_id' => $user->id,
                    'pending_email' => $user->pending_email
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'OTP kodu yenidən göndərildi',
                    'data' => [
                        'otp_sent' => true,
                        'expires_at' => now()->addMinutes(10)->toISOString()
                    ]
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'OTP göndərilə bilmədi',
                    'errors' => [
                        'otp' => ['OTP göndərilməsi zamanı xəta baş verdi']
                    ]
                ], 500);
            }

        } catch (\Exception $e) {
            \Log::error('Resend email change OTP error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'OTP yenidən göndərilməsi zamanı xəta baş verdi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    
    public function getUserProfile()
    {
        try {
            $user = auth()->user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'İstifadəçi tapılmadı'
                ], 401);
            }

            // Return only user's own data without relationships
            $userData = [
                'id' => $user->id,
                'name' => $user->name,
                'surname' => $user->surname,
                'email' => $user->email,
                'fin' => $user->fin,
                'phone' => $user->phone,
            ];

            return response()->json([
                'success' => true,
                'message' => 'İstifadəçi məlumatları uğurla alındı',
                'data' => $userData
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'İstifadəçi məlumatları alınarkən xəta baş verdi',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
