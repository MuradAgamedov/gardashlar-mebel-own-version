<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlogPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'blog_category_id',
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
        return $this->belongsTo(BlogCategory::class, 'blog_category_id');
    }

    public function translates()
    {
        return $this->hasMany(BlogPostTranslate::class);
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

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($blogPost) {
            if (!$blogPost->published_at && request()->has('publish_now')) {
                $blogPost->published_at = now();
            }
        });
    }
}
