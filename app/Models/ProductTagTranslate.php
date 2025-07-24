<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductTagTranslate extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_tag_id',
        'language_id',
        'title',
        'slug'
    ];

    /**
     * Get the product tag that owns this translation
     */
    public function productTag()
    {
        return $this->belongsTo(ProductTag::class, 'product_tag_id');
    }

    /**
     * Get the language for this translation
     */
    public function language()
    {
        return $this->belongsTo(Language::class, 'language_id');
    }
}
