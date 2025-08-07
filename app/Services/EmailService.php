<?php

namespace App\Services;

use App\Models\OtpCode;
use App\Mail\OTPMail;
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

            \Log::info("Attempting to send OTP to email: {$email}, OTP: {$otp}");
            
            // Send actual email
            Mail::to($email)->send(new OTPMail($otp));
            
            \Log::info("OTP email sent successfully to: {$email}, OTP: {$otp}");
            
            return true;
        } catch (\Exception $e) {
            \Log::error("Failed to send OTP to email: {$email}", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Also log to console for immediate debugging
            error_log("Email sending failed for {$email}: " . $e->getMessage());
            
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
        
        \Log::info("OTP verified successfully for email: {$email}, OTP: {$otp}");
        return true;
    }
} 