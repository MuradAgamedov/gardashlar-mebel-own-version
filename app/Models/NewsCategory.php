<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewsCategory extends Model
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
     * Get all translations for this news category
     */
    public function translates()
    {
        return $this->hasMany(NewsCategoryTranslate::class);
    }

    /**
     * Get translation for specific language
     */

    /**
     * Scope for active news categories
     */

    /**
     * Scope for ordered news categories
     */

    /**
     * Get all news posts in this category
     */
    public function newsPosts()
    {
        return $this->hasMany(NewsPost::class);
    }

    /**
     * Boot method for auto-incrementing order
     */
    
}
