<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class OtpCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'contact_info',
        'contact_type',
        'otp_code',
        'expires_at',
        'is_used',
        'purpose'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'is_used' => 'boolean'
    ];

    /**
     * Check if OTP is expired
     */
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    /**
     * Mark OTP as used
     */
    public function markAsUsed(): void
    {
        $this->update(['is_used' => true]);
    }

    /**
     * Find valid OTP by contact info
     */
    public static function findValidOtp(string $contactInfo, string $contactType): ?self
    {
        return self::where('contact_info', $contactInfo)
            ->where('contact_type', $contactType)
            ->where('is_used', false)
            ->where('expires_at', '>', Carbon::now())
            ->latest()
            ->first();
    }

    /**
     * Clean expired OTPs
     */
    public static function cleanExpired(): int
    {
        return self::where('expires_at', '<', Carbon::now())->delete();
    }
}
