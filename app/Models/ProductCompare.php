<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductCompare extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'product_id' => 'integer',
    ];

    /**
     * Get the user that owns this compare
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the product that is compared
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Check if a user has compared a specific product
     */
    public static function isCompared($userId, $productId)
    {
        return static::where('user_id', $userId)
                    ->where('product_id', $productId)
                    ->exists();
    }

    /**
     * Add a product to compare
     */
    public static function addToCompare($userId, $productId)
    {
        return static::firstOrCreate([
            'user_id' => $userId,
            'product_id' => $productId,
        ]);
    }

    /**
     * Remove a product from compare
     */
    public static function removeFromCompare($userId, $productId)
    {
        return static::where('user_id', $userId)
                    ->where('product_id', $productId)
                    ->delete();
    }

    /**
     * Get user's compare list
     */
    public static function getUserCompareList($userId)
    {
        return static::where('user_id', $userId)
                    ->with('product')
                    ->get();
    }

    /**
     * Clear all products from user's compare list
     */
    public static function clearUserCompareList($userId)
    {
        return static::where('user_id', $userId)->delete();
    }

    /**
     * Get compare count for user
     */
    public static function getCompareCount($userId)
    {
        return static::where('user_id', $userId)->count();
    }
}
