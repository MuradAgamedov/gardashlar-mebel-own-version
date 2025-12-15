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
            'full_name.required' => Dictionary::getValue('full_name_required', $request->header('locale', 'az')),
            'phone.nullable' => Dictionary::getValue('phone_nullable', $request->header('locale', 'az')),
            'email.nullable' => Dictionary::getValue('email_nullable', $request->header('locale', 'az')),
            'email.email' => Dictionary::getValue('email_email', $request->header('locale', 'az')),
            'email.unique' => Dictionary::getValue('email_unique', $request->header('locale', 'az')),
            'password.required' => Dictionary::getValue('password_required', $request->header('locale', 'az')),
            'password.min' => Dictionary::getValue('password_min', $request->header('locale', 'az')),
            'password.confirmed' => Dictionary::getValue('password_confirmed', $request->header('locale', 'az')),
            'password_confirmation.required' => Dictionary::getValue('password_confirmation_required', $request->header('locale', 'az')),
            'fin.regex' => Dictionary::getValue('fin_regex', $request->header('locale', 'az')),
        ]);
        

        // Custom validation: either phone or email must be provided
        $validator->after(function ($validator) use ($request) {
            if (empty($request->phone) && empty($request->email)) {
                $validator->errors()->add('contact_info', Dictionary::getValue('contact_info_required'));
            }
        });

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => Dictionary::getValue('validation_failed'),
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
                    'message' => Dictionary::getValue('phone_registration_not_available'),
                    'errors' => [
                        'phone' => [Dictionary::getValue('phone_registration_not_available')]
                    ]
                ], 422);
            }

            // If OTP sending failed, delete the user and return error
            if (!$otpSent) {
                $user->delete(); // Delete the created user
                return response()->json([
                    'success' => false,
                    'message' => Dictionary::getValue('otp_send_failed', $request->header('locale', 'az')),
                    'errors' => [
                        'otp' => [Dictionary::getValue('otp_send_failed')]
                    ]
                ], 500);
            }

            $response = [
                'success' => true,
                'message' => Dictionary::getValue('registration_success_otp_sent'),
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
                'message' => Dictionary::getValue('registration_error', $request->header('locale', 'az')),
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
            'phone.nullable' => Dictionary::getValue('phone_nullable', $request->header('locale', 'az')),
            'email.nullable' => Dictionary::getValue('email_nullable', $request->header('locale', 'az')),
            'email.email' => Dictionary::getValue('email_email', $request->header('locale', 'az')),
        ]);

        // Custom validation: either phone or email must be provided
        $validator->after(function ($validator) use ($request) {
            if (empty($request->phone) && empty($request->email)) {
                $validator->errors()->add('contact_info', Dictionary::getValue('contact_info_required', $request->header('locale', 'az')));
            }
        });

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => Dictionary::getValue('validation_failed', $request->header('locale', 'az')),
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
                        'message' => Dictionary::getValue('otp_sent_to_email', $request->header('locale', 'az')),
                        'data' => [
                            'contact_type' => 'email',
                            'contact_info' => $request->email,
                            'expires_at' => now()->addMinutes(10)->toISOString()
                        ]
                    ]);
                }
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
            \Illuminate\Support\Facades\Mail::to($request->email)->send(new \App\Mail\OTPMail($otp, Dictionary::getValue('password_reset_subject', $request->header('locale', 'az'))));

            \Log::info('Password reset OTP sent', [
                'email' => $request->email,
                'otp' => $otp
            ]);

            return response()->json([
                'success' => true,
                'message' => Dictionary::getValue('password_reset_link_sent', $request->header('locale', 'az')),
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
                'message' => Dictionary::getValue('password_reset_error', $request->header('locale', 'az')),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function verifyResetOTP(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'contact_info' => 'required|string',
            'otp_code' => 'required|string|size:6',
            'contact_type' => 'required|in:email,phone',
            'purpose' => 'nullable|string|in:registration,reset_password,email_change',
        ], [
            'contact_info.required' => Dictionary::getValue('contact_info_required', $request->header('locale', 'az')),
            'otp_code.required' => Dictionary::getValue('otp_code_required', $request->header('locale', 'az')),
            'otp_code.size' => Dictionary::getValue('otp_code_size', $request->header('locale', 'az')),
            'contact_type.required' => Dictionary::getValue('contact_type_required', $request->header('locale', 'az')),
            'contact_type.in' => Dictionary::getValue('contact_type_invalid', $request->header('locale', 'az')),
            'purpose.in' => Dictionary::getValue('purpose_invalid', $request->header('locale', 'az'))
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => Dictionary::getValue('validation_failed', $request->header('locale', 'az')),
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Find the OTP record
            $otpRecord = \App\Models\OtpCode::where('contact_info', $request->contact_info)
                ->where('contact_type', $request->contact_type)
                ->where('is_used', false)
                ->where('expires_at', '>', \Carbon\Carbon::now())
                ->latest()
                ->first();

            if (!$otpRecord || $otpRecord->otp_code !== $request->otp_code) {
                return response()->json([
                    'success' => false,
                    'message' => Dictionary::getValue('invalid_otp', $request->header('locale', 'az')),
                    'errors' => [
                        'otp_code' => [Dictionary::getValue('invalid_otp', $request->header('locale', 'az'))]
                    ]
                ], 422);
            }

            // Mark OTP as used
            $otpRecord->markAsUsed();

            // Generate a temporary reset token
            $resetToken = bin2hex(random_bytes(32));

            // Store reset token in cache for 10 minutes
            \Cache::put("password_reset_token_{$resetToken}", $request->contact_info, 600);

            \Log::info('Password reset OTP verified', [
                'contact_info' => $request->contact_info,
                'reset_token' => $resetToken
            ]);

            return response()->json([
                'success' => true,
                'message' => Dictionary::getValue('password_reset_otp_verified', $request->header('locale', 'az')),
                'data' => [
                    'contact_info' => $request->contact_info,
                    'verified' => true,
                    'reset_token' => $resetToken
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Verify reset OTP error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'contact_info' => $request->contact_info
            ]);

            return response()->json([
                'success' => false,
                'message' => Dictionary::getValue('password_reset_error', $request->header('locale', 'az')),
                'error' => $e->getMessage()
            ], 500);
        }
    }
   
    public function updateProfile(Request $request)
    {
        try {
            $user = auth()->user();

            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|required|string|max:255',
                'surname' => 'sometimes|required|string|max:255',
                'phone' => 'sometimes|nullable|string|max:20',
                'email' => 'sometimes|nullable|email|unique:users,email,' . $user->id,
                'fin' => "nullable"
            ], [
                'name.required' => 'Ad məcburidir',
                'name.string' => 'Ad mətn formatında olmalıdır',
                'name.max' => 'Ad 255 simvoldan çox ola bilməz',
                'surname.required' => 'Soyad məcburidir',
                'surname.string' => 'Soyad mətn formatında olmalıdır',
                'surname.max' => 'Soyad 255 simvoldan çox ola bilməz',
                'phone.string' => 'Telefon nömrəsi mətn formatında olmalıdır',
                'phone.max' => 'Telefon nömrəsi 20 simvoldan çox ola bilməz',
                'email.email' => Dictionary::getValue('email_email', $request->header('locale', 'az')),
                'email.unique' => Dictionary::getValue('email_unique', $request->header('locale', 'az')),
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => Dictionary::getValue('validation_failed', $request->header('locale', 'az')),
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
                    Dictionary::getValue('profile_updated_otp_sent', $request->header('locale', 'az')) :
                    Dictionary::getValue('profile_updated', $request->header('locale', 'az')),
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
                'message' => Dictionary::getValue('profile_update_error', $request->header('locale', 'az')),
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
                    'message' => Dictionary::getValue('validation_failed', $request->header('locale', 'az')),
                    'errors' => $validator->errors()
                ], 422);
            }

            // Check if user has pending email
            if (!$user->pending_email) {
                return response()->json([
                    'success' => false,
                    'message' => Dictionary::getValue('email_change_request_not_found', $request->header('locale', 'az')),
                    'errors' => [
                        'pending_email' => [Dictionary::getValue('no_active_email_change_request', $request->header('locale', 'az'))]
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
                    'message' => Dictionary::getValue('email_change_request_expired', $request->header('locale', 'az')),
                    'errors' => [
                        'expired' => [Dictionary::getValue('please_try_again', $request->header('locale', 'az'))]
                    ]
                ], 422);
            }

            // Verify OTP
            $otpValid = $this->emailService->verifyOTP($user->pending_email, $request->otp_code, 'email_change');

            if (!$otpValid) {
                return response()->json([
                    'success' => false,
                    'message' => Dictionary::getValue('invalid_otp', $request->header('locale', 'az')),
                    'errors' => [
                        'otp_code' => [Dictionary::getValue('invalid_otp', $request->header('locale', 'az'))]
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
                'message' => Dictionary::getValue('email_verified_and_changed', $request->header('locale', 'az')),
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
                'message' => Dictionary::getValue('email_verification_error', $request->header('locale', 'az')),
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
                    'message' => Dictionary::getValue('no_active_email_change_request', $request->header('locale', 'az')),
                    'errors' => [
                        'pending_email' => [Dictionary::getValue('email_change_request_not_found', $request->header('locale', 'az'))]
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
                    'message' => Dictionary::getValue('otp_resent', $request->header('locale', 'az')),
                    'data' => [
                        'otp_sent' => true,
                        'expires_at' => now()->addMinutes(10)->toISOString()
                    ]
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => Dictionary::getValue('otp_send_failed', $request->header('locale', 'az')),
                    'errors' => [
                        'otp' => [Dictionary::getValue('otp_send_error', $request->header('locale', 'az'))]
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
                'message' => Dictionary::getValue('otp_resend_error', $request->header('locale', 'az')),
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
                    'message' => Dictionary::getValue('user_not_found', $request->header('locale', 'az')),
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
                'message' => Dictionary::getValue('user_info_retrieved'),
                'data' => $userData
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => Dictionary::getValue('user_info_retrieval_error'),
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
