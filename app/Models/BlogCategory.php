<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlogCategory extends Model
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
     * Get all translations for this blog category
     */
    public function translates()
    {
        return $this->hasMany(BlogCategoryTranslate::class);
    }

    /**
     * Get translation for specific language
     */

    /**
     * Scope for active blog categories
     */

    /**
     * Scope for ordered blog categories
     */

    /**
     * Get all blog posts in this category
     */
    public function blogPosts()
    {
        return $this->hasMany(BlogPost::class);
    }

    /**
     * Boot method for auto-incrementing order
     */
    
} 