<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductDetailsValuesDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_detail_value_id',
        'order',
    ];

    protected $casts = [
        'order' => 'integer',
    ];

    /**
     * Get the product detail value that owns this detail
     */
    public function productDetailValue()
    {
        return $this->belongsTo(ProductDetailValue::class);
    }

    /**
     * Get the translations for this detail
     */
    public function translates()
    {
        return $this->hasMany(ProductDetailsValuesDetailTranslate::class);
    }

    /**
     * Get a specific translation by language
     */
    public function translate($languageId = null)
    {
        if (!$languageId) {
            $languageId = app()->getLocale();
        }
        
        return $this->hasOne(ProductDetailsValuesDetailTranslate::class)
            ->where('language_id', $languageId);
    }

    /**
     * Scope to order by order field
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    /**
     * Boot method to automatically set order when creating
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($detail) {
            if (!isset($detail->order)) {
                $maxOrder = static::where('product_detail_value_id', $detail->product_detail_value_id)->max('order');
                $detail->order = $maxOrder + 1;
            }
        });
    }
}
