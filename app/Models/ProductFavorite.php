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
        'color_id',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'product_id' => 'integer',
        'color_id' => 'integer',
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
     * Get the color of the favorited product
     */
    public function color()
    {
        return $this->belongsTo(ProductColor::class, 'color_id');
    }

    /**
     * Check if a user has favorited a specific product
     */
    public static function isFavorited($userId, $productId, $colorId = null)
    {
        $query = static::where('user_id', $userId)
                      ->where('product_id', $productId);
        
        if ($colorId !== null) {
            $query->where('color_id', $colorId);
        }
        
        return $query->exists();
    }

    /**
     * Add a product to favorites
     */
    public static function addToFavorites($userId, $productId, $colorId = null)
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
     * Remove a product from favorites
     */
    public static function removeFromFavorites($userId, $productId, $colorId = null)
    {
        $query = static::where('user_id', $userId)
                      ->where('product_id', $productId);
        
        if ($colorId !== null) {
            $query->where('color_id', $colorId);
        }
        
        return $query->delete();
    }
}
