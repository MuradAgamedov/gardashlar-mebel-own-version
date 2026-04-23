<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewsImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'news_post_id',
        'image_url',
        'order',
    ];

    protected $casts = [
        'order' => 'integer',
    ];

    /**
     * Get the news post that owns the image
     */
    public function newsPost()
    {
        return $this->belongsTo(NewsPost::class);
    }

    /**
     * Scope for ordered images
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    /**
     * Accessor for full image URL
     */
    public function getImageUrlAttribute()
    {
        return $this->image_url ? asset('storage/' . $this->image_url) : null;
    }
}
