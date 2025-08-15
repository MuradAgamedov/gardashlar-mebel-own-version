<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductDetailValueTranslate extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_detail_value_id',
        'language_id',
        'keyword',
        'value',
    ];

    /**
     * Get the product detail value that owns this translation
     */
    public function productDetailValue()
    {
        return $this->belongsTo(ProductDetailValue::class);
    }

    /**
     * Get the language for this translation
     */
    public function language()
    {
        return $this->belongsTo(Language::class);
    }

    /**
     * Get the product through the detail value
     */
    public function product()
    {
        return $this->hasOneThrough(Product::class, ProductDetailValue::class, 'id', 'id', 'product_detail_value_id', 'product_id');
    }
}
