<?php

namespace App\Services;

use App\Models\OtpCode;
use App\Mail\OTPMail;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class EmailService
{
    /**
     * Generate OTP code
     */
    public function generateOTP(): string
    {
        return str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Send OTP to email
     */
    public function sendOTP(string $email, string $otp): bool
    {
        try {
            // Store OTP in database
            OtpCode::create([
                'contact_info' => $email,
                'contact_type' => 'email',
                'otp_code' => $otp,
                'expires_at' => Carbon::now()->addMinutes(10), // 10 minutes expiry
                'is_used' => false
            ]);

            // Send actual email using OTPMail class
            Mail::to($email)->send(new OTPMail($otp));
            
            \Log::info("OTP email sent successfully to: {$email}");
            return true;
        } catch (\Exception $e) {
            \Log::error("Failed to send OTP to email: {$email}", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Verify OTP
     */
    public function verifyOTP(string $email, string $otp): bool
    {
        $otpRecord = OtpCode::findValidOtp($email, 'email');
        
        if (!$otpRecord) {
            \Log::warning("No valid OTP found for email: {$email}");
            return false;
        }

        if ($otpRecord->otp_code !== $otp) {
            \Log::warning("Invalid OTP for email: {$email}, provided: {$otp}, expected: {$otpRecord->otp_code}");
            return false;
        }

        // Mark OTP as used
        $otpRecord->markAsUsed();
        
        \Log::info("OTP verified successfully for email: {$email}");
        return true;
    }

    /**
     * Send email (general method)
     */
    public function sendEmail(string $email, string $subject, string $message): array
    {
        try {
            // Send actual email
            Mail::raw($message, function ($mail) use ($email, $subject) {
                $mail->to($email)->subject($subject);
            });

            return [
                'success' => true,
                'message' => 'Email sent successfully',
                'data' => [
                    'email' => $email,
                    'subject' => $subject,
                    'sent_at' => now()->toDateTimeString()
                ]
            ];
        } catch (\Exception $e) {
            \Log::error("Failed to send email to: {$email}", [
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Email sending failed',
                'data' => [
                    'email' => $email,
                    'error' => $e->getMessage()
                ]
            ];
        }
    }
} 