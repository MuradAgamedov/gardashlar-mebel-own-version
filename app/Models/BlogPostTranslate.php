<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlogPostTranslate extends Model
{
    use HasFactory;

    protected $fillable = [
        'blog_post_id',
        'language_id',
        'title',
        'slug',
        'description',
        'card_image_alt_text',
        'card_description',
        'inner_banner_alt_text',
        'seo_title',
        'seo_keywords',
        'seo_description',
    ];

    public function blogPost()
    {
        return $this->belongsTo(BlogPost::class);
    }

    public function language()
    {
        return $this->belongsTo(Language::class);
    }
}
