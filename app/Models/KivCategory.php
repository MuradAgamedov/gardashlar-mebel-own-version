<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KivCategory extends Model
{
    use HasFactory, \App\Traits\Translatable, \App\Traits\HasStatusAndOrder;

    protected $fillable = [
        'order',
        'status',
    ];

    protected $casts = [
        'order' => 'integer',
        'status' => 'boolean',
    ];

    /**
     * Get all translations for this category
     */
    public function translates()
    {
        return $this->hasMany(KivCategoryTranslate::class);
    }

    /**
     * Get translation for specific language
     */

    /**
     * Get all KIVs for this category
     */
    public function kivs()
    {
        return $this->hasMany(Kiv::class);
    }

    /**
     * Scope for active categories
     */

    /**
     * Scope for ordered categories
     */

    /**
     * Boot method for auto-incrementing order
     */
    
} 