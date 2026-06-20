<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductDetailsValuesDetail extends Model
{
    use HasFactory, \App\Traits\Translatable, \App\Traits\HasStatusAndOrder;

    protected $fillable = [
        'product_detail_value_id',
        'order',
    ];

    protected $casts = [
        'order' => 'integer',
    ];

    /**
     * Get the product detail value that owns this detail
     */
    public function productDetailValue()
    {
        return $this->belongsTo(ProductDetailValue::class);
    }

    /**
     * Get the translations for this detail
     */
    public function translates()
    {
        return $this->hasMany(ProductDetailsValuesDetailTranslate::class);
    }

    /**
     * Get a specific translation by language
     */

    /**
     * Scope to order by order field
     */

    /**
     * Boot method to automatically set order when creating
     */
    
}
