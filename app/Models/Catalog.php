<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Catalog extends Model
{
    use HasFactory, App\Traits\Translatable, App\Traits\HasStatusAndOrder;

    protected $fillable = [
        'order',
        'status',
        'catalog_pdf',
    ];

    protected $casts = [
        'order' => 'integer',
        'status' => 'boolean',
    ];

    /**
     * Get all translations for this catalog
     */
    public function translates()
    {
        return $this->hasMany(CatalogTranslate::class);
    }

    /**
     * Get translation for specific language
     */

    /**
     * Scope for active catalogs
     */

    /**
     * Scope for ordered catalogs
     */

    /**
     * Auto-increment order when creating
     */
    
} 