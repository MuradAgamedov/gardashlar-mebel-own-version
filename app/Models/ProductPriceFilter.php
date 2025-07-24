<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductPriceFilter extends Model
{
    use HasFactory;

    protected $fillable = [
        'min_price',
        'max_price',
        'order',
        'status'
    ];

    protected $casts = [
        'status' => 'boolean',
        'order' => 'integer',
        'min_price' => 'decimal:2',
        'max_price' => 'decimal:2'
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($filter) {
            if (!isset($filter->order)) {
                $maxOrder = static::max('order');
                $filter->order = $maxOrder + 1;
            }
        });
    }

    public function translates(): HasMany
    {
        return $this->hasMany(ProductPriceFilterTranslate::class, 'product_price_filter_id');
    }

    public function translate($languageId = null)
    {
        if (!$languageId) {
            $languageId = \App\Models\Language::getMainLanguage()->id;
        }
        
        return $this->translates()->where('language_id', $languageId);
    }

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }
}
