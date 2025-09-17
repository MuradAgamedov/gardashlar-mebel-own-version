<?php

namespace App\Services;

class EmailService
{
    /**
     * Generate OTP code
     */
    public function generateOTP(): string
    {
        return '123456'; // Simple static OTP for testing
    }

    /**
     * Send OTP to email
     */
    public function sendOTP(string $email, string $otp): bool
    {
        // Simple response without actual email sending
        return true;
    }

    /**
     * Verify OTP
     */
    public function verifyOTP(string $email, string $otp): bool
    {
        // Simple response - always return true for testing
        return true;
    }

    /**
     * Send email (general method)
     */
    public function sendEmail(string $email, string $subject, string $message): array
    {
        // Simple response without actual email sending
        return [
            'success' => true,
            'message' => 'Email sent successfully',
            'data' => [
                'email' => $email,
                'subject' => $subject,
                'message' => $message,
                'sent_at' => now()->toDateTimeString()
            ]
        ];
    }
} 