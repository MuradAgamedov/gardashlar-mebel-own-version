<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductDetail extends Model
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
     * Get the product that owns this detail
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get all translations for this product detail
     */
    public function translates()
    {
        return $this->hasMany(ProductDetailTranslate::class);
    }

    /**
     * Get translation for specific language
     */

    /**
     * Scope for ordered details
     */

    /**
     * Auto-increment order when creating
     */
    
}
