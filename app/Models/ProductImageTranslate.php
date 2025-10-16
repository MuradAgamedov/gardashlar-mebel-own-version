<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductImageTranslate extends Model
{
    protected $table = 'product_image_translates';
    
    protected $fillable = [
        'product_image_id',
        'language_id',
        'alt_text'
    ];

    protected $casts = [
        'product_image_id' => 'integer',
        'language_id' => 'integer'
    ];

    /**
     * Product image relationship
     */
    public function productImage(): BelongsTo
    {
        return $this->belongsTo(ProductImage::class);
    }

    /**
     * Language relationship
     */
    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
