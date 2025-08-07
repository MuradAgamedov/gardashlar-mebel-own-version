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
     */
    public function register(Request $request)
    {
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
                $otp = $this->emailService->generateOTP();
                $otpSent = $this->emailService->sendOTP($request->email, $otp);
                $contactType = 'email';
                $contactInfo = $request->email;
            } elseif (!empty($request->phone)) {
                $otp = $this->phoneService->generateOTP();
                $otpSent = $this->phoneService->sendOTP($request->phone, $otp);
                $contactType = 'phone';
                $contactInfo = $request->phone;
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
                $otp = $this->phoneService->generateOTP();
                $sent = $this->phoneService->sendOTP($request->phone, $otp);
                
                if ($sent) {
                    return response()->json([
                        'success' => true,
                        'message' => 'OTP kodu telefon nömrəsinə göndərildi',
                        'data' => [
                            'contact_type' => 'phone',
                            'contact_info' => $request->phone
                        ]
                    ]);
                }
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
     * Verify OTP
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
                $otpVerified = $this->phoneService->verifyOTP($request->phone, $request->otp_code);
                $contactType = 'phone';
                $contactInfo = $request->phone;
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
