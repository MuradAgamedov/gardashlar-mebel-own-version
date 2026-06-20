<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlogPost extends Model
{
    use HasFactory, \App\Traits\Translatable, \App\Traits\HasStatusAndOrder;

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
        return $this->hasMany(BlogImage::class)->ordered();
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

}
