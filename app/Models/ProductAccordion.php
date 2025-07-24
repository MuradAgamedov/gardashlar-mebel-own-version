<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductAccordion extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'order',
        'status'
    ];

    protected $casts = [
        'order' => 'integer',
        'status' => 'boolean'
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($accordion) {
            if (!isset($accordion->order)) {
                $maxOrder = static::where('product_id', $accordion->product_id)->max('order');
                $accordion->order = $maxOrder + 1;
            }
        });
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function translates(): HasMany
    {
        return $this->hasMany(ProductAccordionTranslate::class);
    }

    public function translate($languageId = null)
    {
        if (!$languageId) {
            $languageId = Language::getMainLanguage()->id;
        }
        
        return $this->translates()->where('language_id', $languageId)->first();
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
