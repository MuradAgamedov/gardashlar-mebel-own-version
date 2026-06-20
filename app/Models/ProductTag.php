<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductTag extends Model
{
    use HasFactory, \App\Traits\Translatable, \App\Traits\HasStatusAndOrder;

    protected $fillable = [
        'order',
        'status'
    ];

    protected $casts = [
        'order' => 'integer',
        'status' => 'boolean',
    ];

    /**
     * Get the translations for this product tag
     */
    public function translates()
    {
        return $this->hasMany(ProductTagTranslate::class, 'product_tag_id');
    }

    /**
     * Get translation for current language
     */

    /**
     * Scope for active tags
     */

    /**
     * Scope for ordered tags
     */

    /**
     * Auto-increment order when creating
     */
    
}
