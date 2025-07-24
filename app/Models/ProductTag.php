<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductTag extends Model
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
     * Get the translations for this product tag
     */
    public function translates()
    {
        return $this->hasMany(ProductTagTranslate::class, 'product_tag_id');
    }

    /**
     * Get translation for current language
     */
    public function translate($languageId = null)
    {
        if (!$languageId) {
            $languageId = Language::getMainLanguage()->id ?? 1;
        }
        
        return $this->translates()->where('language_id', $languageId)->first();
    }

    /**
     * Scope for active tags
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Scope for ordered tags
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

        static::creating(function ($productTag) {
            if (!isset($productTag->order)) {
                $maxOrder = static::max('order');
                $productTag->order = $maxOrder + 1;
            }
        });
    }
}
