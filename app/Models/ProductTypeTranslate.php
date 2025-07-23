<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductTypeTranslate extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_type_id',
        'language_id',
        'title',
    ];

    /**
     * Get the product type that owns this translation
     */
    public function productType()
    {
        return $this->belongsTo(ProductType::class);
    }

    /**
     * Get the language for this translation
     */
    public function language()
    {
        return $this->belongsTo(Language::class);
    }
} 