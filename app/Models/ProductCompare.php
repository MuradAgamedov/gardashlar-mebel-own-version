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
        'color_id',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'product_id' => 'integer',
        'color_id' => 'integer',
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
     * Get the color of the compared product
     */
    public function color()
    {
        return $this->belongsTo(ProductColor::class, 'color_id');
    }

    /**
     * Check if a user has compared a specific product
     */
    public static function isCompared($userId, $productId, $colorId = null)
    {
        $query = static::where('user_id', $userId)
                      ->where('product_id', $productId);
        
        if ($colorId !== null) {
            $query->where('color_id', $colorId);
        }
        
        return $query->exists();
    }

    /**
     * Add a product to compare
     */
    public static function addToCompare($userId, $productId, $colorId = null)
    {
        $data = [
            'user_id' => $userId,
            'product_id' => $productId,
        ];
        
        if ($colorId !== null) {
            $data['color_id'] = $colorId;
        }
        
        return static::firstOrCreate($data);
    }

    /**
     * Remove a product from compare
     */
    public static function removeFromCompare($userId, $productId, $colorId = null)
    {
        $query = static::where('user_id', $userId)
                      ->where('product_id', $productId);
        
        if ($colorId !== null) {
            $query->where('color_id', $colorId);
        }
        
        return $query->delete();
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
