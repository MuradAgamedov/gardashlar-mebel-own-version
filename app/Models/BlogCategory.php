<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlogCategory extends Model
{
    use HasFactory;

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
    public function translate($languageId = null)
    {
        if (!$languageId) {
            $languageId = Language::where('is_default', true)->first()->id ?? 1;
        }
        
        return $this->translates()->where('language_id', $languageId)->first();
    }

    /**
     * Scope for active blog categories
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Scope for ordered blog categories
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    /**
     * Boot method for auto-incrementing order
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-increment order on creation
        static::creating(function ($blogCategory) {
            if (!$blogCategory->order) {
                $maxOrder = static::max('order');
                $blogCategory->order = $maxOrder + 1;
            }
        });
    }
} 