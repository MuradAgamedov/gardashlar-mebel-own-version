<?php

namespace App\Services;

use App\Models\OtpCode;
use Carbon\Carbon;

class PhoneService
{
    /**
     * Generate OTP code
     */
    public function generateOTP(): string
    {
        return str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Send OTP to phone
     */
    public function sendOTP(string $phone, string $otp): bool
    {
        try {
            // Store OTP in database
            OtpCode::create([
                'contact_info' => $phone,
                'contact_type' => 'phone',
                'otp_code' => $otp,
                'expires_at' => Carbon::now()->addMinutes(10), // 10 minutes expiry
                'is_used' => false
            ]);

            // TODO: Implement SMS sending
            // For now, just log the OTP
            \Log::info("OTP sent to phone: {$phone}, OTP: {$otp}");
            return true;
        } catch (\Exception $e) {
            \Log::error("Failed to send OTP to phone: {$phone}", [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Verify OTP
     */
    public function verifyOTP(string $phone, string $otp): bool
    {
        $otpRecord = OtpCode::findValidOtp($phone, 'phone');
        
        if (!$otpRecord) {
            return false;
        }

        if ($otpRecord->otp_code !== $otp) {
            return false;
        }

        // Mark OTP as used
        $otpRecord->markAsUsed();
        
        \Log::info("OTP verified for phone: {$phone}, OTP: {$otp}");
        return true;
    }
} 