<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HomePageTypeProduct extends Model
{
    use HasFactory, \App\Traits\HasStatusAndOrder;

    protected $fillable = [
        'product_type_id',
        'product_id',
        'order',
        'status'
    ];

    protected $casts = [
        'product_type_id' => 'integer',
        'product_id' => 'integer',
        'order' => 'integer',
        'status' => 'boolean',
    ];

    /**
     * Get the product type
     */
    public function productType()
    {
        return $this->belongsTo(ProductType::class);
    }

    /**
     * Get the product
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Scope for active items
     */

    /**
     * Scope for ordered items
     */

    /**
     * Auto-increment order when creating
     */
    
}
