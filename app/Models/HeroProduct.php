<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HeroProduct extends Model
{
    use HasFactory, App\Traits\HasStatusAndOrder;

    protected $fillable = [
        'product_id',
        'order',
        'status',
    ];

    protected $casts = [
        'order' => 'integer',
        'status' => 'boolean',
    ];

    /**
     * Get the product that owns this hero product
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Scope for active hero products
     */

    /**
     * Scope for ordered hero products
     */
    
} 