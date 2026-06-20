<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\EmailService;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\JsonResponse;

class RegisterController extends Controller
{
    protected EmailService $emailService;

    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }

    /**
     * Register a new user
     */
    public function register(Request $request): JsonResponse
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
            return $this->error(__('Validation failed'), 422, ['errors' => $errors]);
        }

        // Check if user already exists
        $existingUser = User::where('email', $email)->first();
        if ($existingUser) {
            return $this->error('user_already_exists', 422, ['error' => __('A user with this email already exists')]);
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
                return $this->created([
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
                ], __('User registered successfully, OTP sent to email'));
            } else {
                // Delete user if email sending failed
                $user->delete();
                return $this->error(__('Registration failed - could not send OTP'), 500, ['error' => __('Email sending failed')]);
            }
        } catch (\Exception $e) {
            return $this->error(__('Registration failed'), 500, ['error' => __('Could not create user') . ': ' . $e->getMessage()]);
        }
    }

    /**
     * Verify OTP and mark email as verified
     */
    public function verifyOtp(Request $request): JsonResponse
    {
        $email = $request->input('email');
        $otpCode = $request->input('otp_code');

        // Check if email and OTP code are provided
        if (empty($email)) {
            return $this->error(__('Email is required'), 422, ['error' => __('Email field cannot be empty')]);
        }

        if (empty($otpCode)) {
            return $this->error(__('OTP code is required'), 422, ['error' => __('OTP code field cannot be empty')]);
        }

        // Find user by email
        $user = User::where('email', $email)->first();
        if (!$user) {
            return $this->notFound(__('User not found'));
        }

        // Verify OTP using EmailService
        $isVerified = $this->emailService->verifyOTP((string)$email, (string)$otpCode, 'registration');

        if ($isVerified) {
            // Mark email as verified
            $user->email_verified_at = now();
            $user->save();

            // Generate authentication token (automatic login)
            $token = $user->createToken('auth_token')->plainTextToken;

            return $this->success([
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
            ], __('Email verified and logged in successfully'));
        } else {
            return $this->error(__('OTP verification failed'), 422, [
                'email' => $email,
                'verified' => false,
                'error' => __('Invalid or expired OTP code')
            ]);
        }
    }
}
