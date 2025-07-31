<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductType extends Model
{
    use HasFactory;

    protected $fillable = [
        'order',
        'status'
    ];

    protected $casts = [
        'order' => 'integer',
        'status' => 'boolean',
    ];

    /**
     * Get all translations for this product type
     */
    public function translates()
    {
        return $this->hasMany(ProductTypeTranslate::class);
    }

    /**
     * Get all products that have this type
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_product_type');
    }

    /**
     * Get translation for specific language
     */
    public function translate($languageId = null)
    {
        if (!$languageId) {
            $languageId = Language::getMainLanguage()->id;
        }
        
        return $this->translates()->where('language_id', $languageId)->first();
    }

    /**
     * Scope for active types
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Scope for ordered types
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

        static::creating(function ($type) {
            if (!isset($type->order)) {
                $maxOrder = static::max('order');
                $type->order = $maxOrder + 1;
            }
        });
    }
} 