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
        $password = $request->input('password');
        $passwordConfirmation = $request->input('password_confirmation');

        // Validation errors array
        $errors = [];

        // Check required fields
        if (empty($name)) {
            $errors['name'] = 'Name is required';
        }

        if (empty($surname)) {
            $errors['surname'] = 'Surname is required';
        }

        if (!empty($phone) && !preg_match('/^(?:\+\d{1,3})?\d{1,4}\d{7,10}$/', $phone)) {
            $errors['phone'] = 'Invalid phone number format';
        }

        if (empty($email)) {
            $errors['email'] = 'Email is required';
        } elseif (!preg_match('/^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}$/i', $email)) {
            $errors['email'] = 'Invalid email format';
        }

        if (empty($password)) {
            $errors['password'] = 'Password is required';
        } else {
            // Password validation with regex
            if (strlen($password) < 8) {
                $errors['password'] = 'Password must be at least 8 characters long';
            } elseif (!preg_match('/[!@#$%^&*()_+\-=\[\]{};\':"\\|,.<>\/?]/', $password)) {
                $errors['password'] = 'Password must contain at least one special character';
            } elseif (!preg_match('/\d/', $password)) {
                $errors['password'] = 'Password must contain at least one number';
            } elseif (!preg_match('/[A-Z]/', $password)) {
                $errors['password'] = 'Password must contain at least one uppercase letter';
            }
        }

        if (empty($passwordConfirmation)) {
            $errors['password_confirmation'] = 'Password confirmation is required';
        } elseif ($password !== $passwordConfirmation) {
            $errors['password_confirmation'] = 'Password confirmation does not match';
        }

        // Return validation errors if any
        if (!empty($errors)) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
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
                'message' => 'User already exists',
                'data' => [
                    'error' => 'A user with this email already exists'
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
                'password' => Hash::make($password),
                'email_verified_at' => null, // Not verified yet
            ]);

            // Generate OTP token
            $otpToken = $this->emailService->generateOTP();
            
            // Send OTP to email
            $emailSent = $this->emailService->sendOTP($email, $otpToken);

            if ($emailSent) {
                return response()->json([
                    'success' => true,
                    'message' => 'User registered successfully, OTP sent to email',
                    'data' => [
                        'user' => [
                            'id' => $user->id,
                            'name' => $user->name,
                            'surname' => $user->surname,
                            'phone' => $user->phone,
                            'email' => $user->email,
                            'email_verified' => false
                        ],
                        'otp_sent' => true,
                        'message' => 'Please verify your email with the OTP code'
                    ]
                ], 201);
            } else {
                // Delete user if email sending failed
                $user->delete();
                return response()->json([
                    'success' => false,
                    'message' => 'Registration failed - could not send OTP',
                    'data' => [
                        'error' => 'Email sending failed'
                    ]
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Registration failed',
                'data' => [
                    'error' => 'Could not create user: ' . $e->getMessage()
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
                'message' => 'Email is required',
                'data' => [
                    'error' => 'Email field cannot be empty'
                ]
            ], 422);
        }

        if (empty($otpCode)) {
            return response()->json([
                'success' => false,
                'message' => 'OTP code is required',
                'data' => [
                    'error' => 'OTP code field cannot be empty'
                ]
            ], 422);
        }

        // Find user by email
        $user = User::where('email', $email)->first();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
                'data' => [
                    'error' => 'No user found with this email address'
                ]
            ], 404);
        }

        // Verify OTP using EmailService
        $isVerified = $this->emailService->verifyOTP((string)$email, (string)$otpCode);

        if ($isVerified) {
            // Mark email as verified
            $user->email_verified_at = now();
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Email verified successfully',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'surname' => $user->surname,
                        'phone' => $user->phone,
                        'email' => $user->email,
                        'email_verified' => true
                    ],
                    'verified' => true,
                    'message' => 'Your email has been verified successfully'
                ]
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'OTP verification failed',
                'data' => [
                    'email' => $email,
                    'verified' => false,
                    'error' => 'Invalid or expired OTP code'
                ]
            ], 422);
        }
    }
}
