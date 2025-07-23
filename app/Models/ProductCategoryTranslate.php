<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductCategoryTranslate extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_category_id',
        'language_id',
        'title',
        'slug',
        'seo_title',
        'seo_keywords',
        'seo_description',
        'image_alt_text',
    ];

    protected $casts = [
        'product_category_id' => 'integer',
        'language_id' => 'integer',
    ];

    /**
     * Get the product category that owns this translation
     */
    public function productCategory()
    {
        return $this->belongsTo(ProductCategory::class);
    }

    /**
     * Get the language for this translation
     */
    public function language()
    {
        return $this->belongsTo(Language::class);
    }
} 