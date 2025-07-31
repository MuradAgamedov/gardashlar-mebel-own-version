<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HomePageTypeProduct extends Model
{
    use HasFactory;

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
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Scope for ordered items
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    /**
     * Auto-increment order when creating
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($item) {
            if (!isset($item->order)) {
                $maxOrder = static::where('product_type_id', $item->product_type_id)->max('order');
                $item->order = $maxOrder + 1;
            }
        });
    }
}
