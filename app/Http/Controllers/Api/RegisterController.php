<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\EmailService;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    protected $emailService;

    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }

    /**
     * Register a new user
     */
    public function register(Request $request)
    {
        // Get and validate input fields
        $name = trim($request->input('name'));
        $surname = trim($request->input('surname'));
        $phone = trim($request->input('phone'));
        $email = trim($request->input('email'));
        $fin = trim((string)$request->input('fin'));
        $password = $request->input('password');
        $passwordConfirmation = $request->input('password_confirmation');

        // Validation errors array
        $errors = [];

        // Check required fields
        if (empty($name)) {
            $errors['name'] = __('Name is required');
        }

        if (empty($surname)) {
            $errors['surname'] = __('Surname is required');
        }

        if (!empty($phone) && !preg_match('/^(?:\+\d{1,3}[-\s]?)?(?:\(?\d{2,4}\)?[-\s]?){2,4}\d{2,4}$/', $phone)) {
            $errors['phone'] = __('Invalid phone number format');
        }
        
        // Optional FIN validation (alphanumeric and - _ . allowed, 2-50 chars)
        if (!empty($fin) && !preg_match('/^[A-Za-z0-9._-]{2,50}$/', $fin)) {
            $errors['fin'] = __('Invalid FIN format');
        }
        

        if (empty($email)) {
            $errors['email'] = __('Email is required');
        } elseif (!preg_match('/^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}$/i', $email)) {
            $errors['email'] = __('Invalid email format');
        }

        if (empty($password)) {
            $errors['password'] = __('Password is required');
        } else {
            // Password validation with regex
            if (strlen($password) < 8) {
                $errors['password'] = __('Password must be at least 8 characters long');
            } elseif (!preg_match('/[!@#$%^&*()_+\-=\[\]{};\':"\\|,.<>\/?]/', $password)) {
                $errors['password'] = __('Password must contain at least one special character');
            } elseif (!preg_match('/\d/', $password)) {
                $errors['password'] = __('Password must contain at least one number');
            } elseif (!preg_match('/[A-Z]/', $password)) {
                $errors['password'] = __('Password must contain at least one uppercase letter');
            }
        }

        if (empty($passwordConfirmation)) {
            $errors['password_confirmation'] = __('Password confirmation is required');
        } elseif ($password !== $passwordConfirmation) {
            $errors['password_confirmation'] = __('Password confirmation does not match');
        }

        // Return validation errors if any
        if (!empty($errors)) {
            return response()->json([
                'success' => false,
                'message' => __('Validation failed'),
                'data' => [
                    'errors' => $errors
                ]
            ], 422);
        }

        // Check if user already exists
        $existingUser = User::where('email', $email)->first();
        if ($existingUser) {
            return response()->json([
                'success' => false,
                'message' => 'user_already_exists',
                'data' => [
                    'error' => __('A user with this email already exists')
                ]
            ], 422);
        }

        // Save user to database immediately (but unverified)
        try {
            $user = User::create([
                'name' => $name,
                'surname' => $surname,
                'phone' => $phone,
                'email' => $email,
                'fin' => $fin ?: null,
                'password' => Hash::make($password),
                'email_verified_at' => null, // Not verified yet
            ]);

            // Send OTP to email (OTP generation is handled inside sendOTP method)
            $emailSent = $this->emailService->sendOTP($email, 'registration');

            if ($emailSent) {
                return response()->json([
                    'success' => true,
                    'message' => __('User registered successfully, OTP sent to email'),
                    'data' => [
                        'user' => [
                            'id' => $user->id,
                            'name' => $user->name,
                            'surname' => $user->surname,
                            'phone' => $user->phone,
                            'email' => $user->email,
                            'fin' => $user->fin,
                            'email_verified' => false
                        ],
                        'otp_sent' => true,
                        'expires_at' => now()->addMinutes(10)->toISOString(),
                        'message' => __('Please verify your email with the OTP code')
                    ]
                ], 201);
            } else {
                // Delete user if email sending failed
                $user->delete();
                return response()->json([
                    'success' => false,
                    'message' => __('Registration failed - could not send OTP'),
                    'data' => [
                        'error' => __('Email sending failed')
                    ]
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Registration failed'),
                'data' => [
                    'error' => __('Could not create user') . ': ' . $e->getMessage()
                ]
            ], 500);
        }
    }

    /**
     * Verify OTP and mark email as verified
     */
    public function verifyOtp(Request $request)
    {
        $email = $request->input('email');
        $otpCode = $request->input('otp_code');

        // Check if email and OTP code are provided
        if (empty($email)) {
            return response()->json([
                'success' => false,
                'message' => __('Email is required'),
                'data' => [
                    'error' => __('Email field cannot be empty')
                ]
            ], 422);
        }

        if (empty($otpCode)) {
            return response()->json([
                'success' => false,
                'message' => __('OTP code is required'),
                'data' => [
                    'error' => __('OTP code field cannot be empty')
                ]
            ], 422);
        }

        // Find user by email
        $user = User::where('email', $email)->first();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => __('User not found'),
                'data' => [
                    'error' => __('No user found with this email address')
                ]
            ], 404);
        }

        // Verify OTP using EmailService
        $isVerified = $this->emailService->verifyOTP((string)$email, (string)$otpCode, 'registration');

        if ($isVerified) {
            // Mark email as verified
            $user->email_verified_at = now();
            $user->save();

            // Generate authentication token (automatic login)
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => __('Email verified and logged in successfully'),
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'surname' => $user->surname,
                        'phone' => $user->phone,
                        'email' => $user->email,
                        'fin' => $user->fin,
                        'email_verified' => true,
                        'email_verified_at' => $user->email_verified_at->toDateTimeString()
                    ],
                    'token' => $token,
                    'token_type' => 'Bearer',
                    'verified' => true,
                    'logged_in' => true,
                    'verification_date' => $user->email_verified_at->toDateTimeString(),
                    'message' => __('Registration completed and logged in successfully')
                ]
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => __('OTP verification failed'),
                'data' => [
                    'email' => $email,
                    'verified' => false,
                    'error' => __('Invalid or expired OTP code')
                ]
            ], 422);
        }
    }
}
