<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlogCategoryTranslate extends Model
{
    use HasFactory;

    protected $fillable = [
        'blog_category_id',
        'language_id',
        'title',
        'seo_title',
        'seo_keywords',
        'seo_description',
    ];

    /**
     * Get the blog category that owns the translation
     */
    public function blogCategory()
    {
        return $this->belongsTo(BlogCategory::class);
    }

    /**
     * Get the language that owns the translation
     */
    public function language()
    {
        return $this->belongsTo(Language::class);
    }
} 