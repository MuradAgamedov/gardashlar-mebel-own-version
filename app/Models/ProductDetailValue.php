<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductDetailValue extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_detail_id',
        'order',
    ];

    protected $casts = [
        'order' => 'integer',
    ];

    public function productDetail()
    {
        return $this->belongsTo(ProductDetail::class);
    }

    public function translates()
    {
        return $this->hasMany(ProductDetailValueTranslate::class);
    }

    public function translate($languageId = null)
    {
        if (!$languageId) {
            $languageId = Language::where('is_default', true)->first()->id ?? 1;
        }
        return $this->translates()->where('language_id', $languageId)->first();
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($value) {
            if (!isset($value->order)) {
                $maxOrder = static::where('product_detail_id', $value->product_detail_id)->max('order');
                $value->order = $maxOrder + 1;
            }
        });
    }
}
