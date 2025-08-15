<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductDetailValue extends Model
{
    use HasFactory;

    protected $fillable = [
        'order',
        'keyword',
    ];

    protected $casts = [
        'order' => 'integer',
    ];

    /**
     * Get all translations for this product detail value
     */
    public function translates()
    {
        return $this->hasMany(ProductDetailValueTranslate::class);
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
     * Scope for ordered values
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

        static::creating(function ($value) {
            if (!isset($value->order)) {
                $maxOrder = static::max('order');
                $value->order = $maxOrder + 1;
            }
        });
    }
}
