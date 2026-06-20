<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductDetailValue extends Model
{
    use HasFactory, \App\Traits\Translatable, \App\Traits\HasStatusAndOrder;

    protected $fillable = [
        'product_id',
        'order',
    ];

    protected $casts = [
        'order' => 'integer',
    ];

    /**
     * Get the product that owns this detail value
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

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

    /**
     * Get all details for this product detail value
     */
    public function details()
    {
        return $this->hasMany(ProductDetailsValuesDetail::class);
    }

    /**
     * Scope for ordered values
     */

    /**
     * Auto-increment order when creating
     */
    
}
