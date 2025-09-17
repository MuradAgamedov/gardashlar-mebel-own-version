<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\EmailService;
use Illuminate\Http\Request;

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
        // Validate required fields
        $email = $request->input('email');
        
        // Check if email is provided
        if (empty($email)) {
            return response()->json([
                'success' => false,
                'message' => 'Email is required',
                'data' => [
                    'error' => 'Email field cannot be empty',
                    'received_data' => $request->all()
                ]
            ], 422);
        }

        // Get registration data from frontend
        $userData = [
            'name' => $request->input('name'),
            'surname' => $request->input('surname'), 
            'phone' => $request->input('phone'),
            'email' => $email,
            'password' => $request->input('password'),
        ];

        // Generate OTP token
        $otpToken = $this->emailService->generateOTP();
        
        // Send OTP to email (with string type guarantee)
        $emailSent = $this->emailService->sendOTP((string)$userData['email'], $otpToken);

        if ($emailSent) {
            return response()->json([
                'success' => true,
                'message' => 'Registration data received and OTP sent to email',
                'data' => [
                    'user' => $userData,
                    'otp_sent' => true,
                    'email' => $userData['email'],
                    'message' => 'OTP token sent to your email address'
                ]
            ], 201);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Registration failed - could not send OTP',
                'data' => [
                    'user' => $userData,
                    'otp_sent' => false,
                    'error' => 'Email sending failed'
                ]
            ], 500);
        }
    }

    /**
     * Verify OTP
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

        // Verify OTP using EmailService (with string type guarantee)
        $isVerified = $this->emailService->verifyOTP((string)$email, (string)$otpCode);

        if ($isVerified) {
            return response()->json([
                'success' => true,
                'message' => 'OTP verified successfully',
                'data' => [
                    'email' => $email,
                    'verified' => true,
                    'message' => 'Your email has been verified'
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
