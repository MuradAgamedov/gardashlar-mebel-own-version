<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductDetailTranslate extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_detail_id',
        'language_id',
        'keyword',
        'value',
    ];

    /**
     * Get the product detail that owns this translation
     */
    public function productDetail()
    {
        return $this->belongsTo(ProductDetail::class);
    }

    /**
     * Get the language for this translation
     */
    public function language()
    {
        return $this->belongsTo(Language::class);
    }

    /**
     * Get the product through the detail
     */
    public function product()
    {
        return $this->hasOneThrough(Product::class, ProductDetail::class, 'id', 'id', 'product_detail_id', 'product_id');
    }
}
