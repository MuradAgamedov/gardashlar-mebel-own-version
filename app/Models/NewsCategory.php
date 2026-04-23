<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewsCategory extends Model
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
     * Get all translations for this news category
     */
    public function translates()
    {
        return $this->hasMany(NewsCategoryTranslate::class);
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
     * Scope for active news categories
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Scope for ordered news categories
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

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
    protected static function boot()
    {
        parent::boot();

        // Auto-increment order on creation
        static::creating(function ($newsCategory) {
            if (!$newsCategory->order) {
                $maxOrder = static::max('order');
                $newsCategory->order = $maxOrder + 1;
            }
        });
    }
}
