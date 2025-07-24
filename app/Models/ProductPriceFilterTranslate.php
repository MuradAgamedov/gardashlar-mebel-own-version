<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductPriceFilterTranslate extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_price_filter_id',
        'language_id',
        'title'
    ];

    public function productPriceFilter(): BelongsTo
    {
        return $this->belongsTo(ProductPriceFilter::class, 'product_price_filter_id');
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
