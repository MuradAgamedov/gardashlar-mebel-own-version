<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'order',
    ];

    protected $casts = [
        'order' => 'integer',
    ];

    /**
     * Get the product that owns this detail
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get all translations for this product detail
     */
    public function translates()
    {
        return $this->hasMany(ProductDetailTranslate::class);
    }

    /**
     * Get translation for specific language
     */
    public function translate($languageId = null)
    {
        if (!$languageId) {
            $languageId = Language::where('is_default', true)->first()->id ?? 1;
        }
        
        return $this->translates()->where('language_id', $languageId)->first();
    }

    /**
     * Scope for ordered details
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    /**
     * Auto-increment order when creating
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($detail) {
            if (!isset($detail->order)) {
                $maxOrder = static::where('product_id', $detail->product_id)->max('order');
                $detail->order = $maxOrder + 1;
            }
        });
    }
}
