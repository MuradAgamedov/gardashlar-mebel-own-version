<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    use HasFactory, App\Traits\HasStatusAndOrder;

    protected $fillable = [
        'code',
        'course',
        'order',
        'status'
    ];

    protected $casts = [
        'course' => 'decimal:4',
        'status' => 'boolean'
    ];

    protected $hidden = ['created_at', 'updated_at'];

    /**
     * Scope for active currencies
     */

    /**
     * Scope for ordered currencies
     */

    /**
     * Get currency by code
     */
    public static function getByCode(string $code)
    {
        return self::active()->where('code', strtoupper($code))->first();
    }

    /**
     * Check if currency exists by code
     */
    public static function existsByCode(string $code): bool
    {
        return self::active()->where('code', strtoupper($code))->exists();
    }

    /**
     * Get all available currency codes
     */
    public static function getAvailableCodes(): array
    {
        return self::active()->pluck('code')->toArray();
    }

    /**
     * Get currencies as array for frontend
     */
    public static function getForFrontend(): array
    {
        return self::active()
            ->ordered()
            ->get()
            ->map(function ($currency) {
                return [
                    'code' => $currency->code,
                    'course' => $currency->course
                ];
            })
            ->toArray();
    }

    /**
     * Convert amount from one currency to another
     */
    public static function convert(float $amount, string $fromCode, string $toCode): float
    {
        $fromCurrency = self::getByCode($fromCode);
        $toCurrency = self::getByCode($toCode);

        if (!$fromCurrency || !$toCurrency) {
            return $amount;
        }

        // Convert to base currency (AZN) first, then to target currency
        $baseAmount = $amount * $fromCurrency->course;
        return $baseAmount / $toCurrency->course;
    }

    /**
     * Get default currency (AZN)
     */
    public static function getDefault()
    {
        return self::getByCode('AZN') ?? self::active()->first();
    }
}
