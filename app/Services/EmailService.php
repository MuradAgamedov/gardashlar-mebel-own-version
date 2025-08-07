<?php

namespace App\Services;

use App\Models\OtpCode;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
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

            // For now, we'll just log the OTP
            // In production, you would send actual email
            \Log::info("OTP sent to email: {$email}, OTP: {$otp}");
            
            // TODO: Implement actual email sending
            // Mail::to($email)->send(new OTPMail($otp));
            
            return true;
        } catch (\Exception $e) {
            \Log::error("Failed to send OTP to email: {$email}", [
                'error' => $e->getMessage()
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
            return false;
        }

        if ($otpRecord->otp_code !== $otp) {
            return false;
        }

        // Mark OTP as used
        $otpRecord->markAsUsed();
        
        \Log::info("OTP verified for email: {$email}, OTP: {$otp}");
        return true;
    }
} 