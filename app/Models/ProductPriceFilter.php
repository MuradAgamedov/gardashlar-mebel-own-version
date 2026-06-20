<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductPriceFilter extends Model
{
    use HasFactory, App\Traits\Translatable, App\Traits\HasStatusAndOrder;

    protected $fillable = [
        'min_price',
        'max_price',
        'order',
        'status'
    ];

    protected $casts = [
        'status' => 'boolean',
        'order' => 'integer',
        'min_price' => 'decimal:2',
        'max_price' => 'decimal:2'
    ];

    public function translates(): HasMany
    {
        return $this->hasMany(ProductPriceFilterTranslate::class, 'product_price_filter_id');
    }

}
