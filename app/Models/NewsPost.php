<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewsPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'news_category_id',
        'card_image',
        'inner_banner_image',
        'published_at',
        'status',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'status' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(NewsCategory::class, 'news_category_id');
    }

    public function translates()
    {
        return $this->hasMany(NewsPostTranslate::class);
    }

    public function translate($languageId = null)
    {
        if (!$languageId) {
            $languageId = Language::where('is_default', true)->first()->id ?? 1;
        }
        return $this->translates()->where('language_id', $languageId)->first();
    }

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function scopePublished($query)
    {
        return $query->where('status', true)
                    ->where(function($q) {
                        $q->whereNull('published_at')
                          ->orWhere('published_at', '<=', now());
                    });
    }

    public function images()
    {
        return $this->hasMany(NewsImage::class)->ordered();
    }

    // Accessors
    public function getCardImageUrlAttribute()
    {
        return $this->card_image ? asset('storage/' . $this->card_image) : null;
    }

    public function getInnerBannerImageUrlAttribute()
    {
        return $this->inner_banner_image ? asset('storage/' . $this->inner_banner_image) : null;
    }

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($newsPost) {
            if (!$newsPost->published_at && request()->has('publish_now')) {
                $newsPost->published_at = now();
            }
        });
    }
}
