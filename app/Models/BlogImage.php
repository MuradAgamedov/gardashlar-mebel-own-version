<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlogImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'blog_post_id',
        'image_url',
        'order',
    ];

    protected $casts = [
        'order' => 'integer',
    ];

    public function blogPost()
    {
        return $this->belongsTo(BlogPost::class);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($blogImage) {
            if (!$blogImage->order) {
                $maxOrder = static::where('blog_post_id', $blogImage->blog_post_id)->max('order');
                $blogImage->order = $maxOrder + 1;
            }
        });
    }
}
