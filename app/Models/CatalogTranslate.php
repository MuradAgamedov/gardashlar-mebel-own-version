<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatalogTranslate extends Model
{
    use HasFactory;

    protected $fillable = [
        'catalog_id',
        'language_id',
        'catalog_year',
    ];

    protected $casts = [
        'catalog_id' => 'integer',
        'language_id' => 'integer',
    ];

    /**
     * Get the catalog that owns this translation
     */
    public function catalog()
    {
        return $this->belongsTo(Catalog::class);
    }

    /**
     * Get the language for this translation
     */
    public function language()
    {
        return $this->belongsTo(Language::class);
    }
} 