<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductDetailValueTranslate extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_detail_value_id',
        'language_id',
        'keyword',
        'value',
    ];

    public function productDetailValue()
    {
        return $this->belongsTo(ProductDetailValue::class);
    }

    public function language()
    {
        return $this->belongsTo(Language::class);
    }

    public function productDetail()
    {
        return $this->hasOneThrough(ProductDetail::class, ProductDetailValue::class, 'id', 'id', 'product_detail_value_id', 'product_detail_id');
    }
}
