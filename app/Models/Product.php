<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'views_count',
        'order',
        'status',
        'discount_percent'
    ];

    protected $casts = [
        'views_count' => 'integer',
        'order' => 'integer',
        'status' => 'boolean',
        'discount_percent' => 'decimal:2'
    ];

    // Relationships
    public function categories()
    {
        return $this->belongsToMany(ProductCategory::class, 'product_product_category');
    }

    public function types()
    {
        return $this->belongsToMany(ProductType::class, 'product_product_type');
    }

    public function translates()
    {
        return $this->hasMany(ProductTranslate::class, 'product_id');
    }

    public function tags()
    {
        return $this->belongsToMany(ProductTag::class, 'product_product_tag', 'product_id', 'product_tag_id');
    }

    public function accordions()
    {
        return $this->hasMany(ProductAccordion::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    public function scopeWithDiscount($query)
    {
        return $query->where('discount_percent', '>', 0);
    }

    // Helper methods
    public function translate($languageId = null)
    {
        if (!$languageId) {
            $languageId = Language::where('is_default', true)->first()->id ?? 1;
        }
        
        return $this->translates()->where('language_id', $languageId)->first();
    }

    public function getTitleAttribute()
    {
        $translation = $this->translate();
        return $translation ? $translation->title : 'No title';
    }

    public function getDescriptionAttribute()
    {
        $translation = $this->translate();
        return $translation ? $translation->description : null;
    }

    public function getSlugAttribute()
    {
        $translation = $this->translate();
        return $translation ? $translation->slug : 'no-slug';
    }

    // Auto-increment order
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($product) {
            if (!isset($product->order)) {
                $maxOrder = static::max('order');
                $product->order = $maxOrder + 1;
            }
        });
    }
}
