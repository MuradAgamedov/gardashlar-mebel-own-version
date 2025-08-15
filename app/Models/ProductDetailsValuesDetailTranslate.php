<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductDetailsValuesDetailTranslate extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_details_values_detail_id',
        'language_id',
        'keyword',
        'value',
    ];

    /**
     * Get the detail that owns this translation
     */
    public function detail()
    {
        return $this->belongsTo(ProductDetailsValuesDetail::class, 'product_details_values_detail_id');
    }

    /**
     * Get the language for this translation
     */
    public function language()
    {
        return $this->belongsTo(Language::class);
    }

    /**
     * Get the product detail value through the detail
     */
    public function productDetailValue()
    {
        return $this->hasOneThrough(
            ProductDetailValue::class, 
            ProductDetailsValuesDetail::class, 
            'id', 
            'id', 
            'product_details_values_detail_id', 
            'product_detail_value_id'
        );
    }

    /**
     * Get the product through the detail value
     */
    public function product()
    {
        return $this->hasOneThrough(
            Product::class, 
            ProductDetailValue::class, 
            'id', 
            'id', 
            'product_detail_value_id', 
            'product_id'
        );
    }
}
