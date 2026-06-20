<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductType extends Model
{
    use HasFactory, \App\Traits\Translatable, \App\Traits\HasStatusAndOrder;

    protected $fillable = [
        'order',
        'status'
    ];

    protected $casts = [
        'order' => 'integer',
        'status' => 'boolean',
    ];

    /**
     * Get all translations for this product type
     */
    public function translates()
    {
        return $this->hasMany(ProductTypeTranslate::class);
    }

    /**
     * Get all products that have this type
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_product_type');
    }

    /**
     * Get products that are shown on homepage for this type
     */
    public function homePageProducts()
    {
        return $this->hasMany(HomePageTypeProduct::class)->with('product');
    }

    /**
     * Get translation for specific language
     */

    /**
     * Scope for active types
     */

    /**
     * Scope for ordered types
     */

    /**
     * Auto-increment order when creating
     */
    
} 