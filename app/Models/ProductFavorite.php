<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductFavorite extends Model
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
     * Get the user that owns this favorite
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the product that is favorited
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Check if a user has favorited a specific product
     */
    public static function isFavorited($userId, $productId)
    {
        return static::where('user_id', $userId)
                    ->where('product_id', $productId)
                    ->exists();
    }

    /**
     * Add a product to favorites
     */
    public static function addToFavorites($userId, $productId)
    {
        return static::firstOrCreate([
            'user_id' => $userId,
            'product_id' => $productId,
        ]);
    }

    /**
     * Remove a product from favorites
     */
    public static function removeFromFavorites($userId, $productId)
    {
        return static::where('user_id', $userId)
                    ->where('product_id', $productId)
                    ->delete();
    }
}
