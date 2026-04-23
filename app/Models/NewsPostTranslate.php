<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewsPostTranslate extends Model
{
    use HasFactory;

    protected $fillable = [
        'news_post_id',
        'language_id',
        'title',
        'slug',
        'card_description',
        'description',
        'seo_title',
        'seo_keywords',
        'seo_description',
    ];

    /**
     * Get the news post that owns the translate
     */
    public function newsPost()
    {
        return $this->belongsTo(NewsPost::class);
    }

    /**
     * Get the language that owns the translate
     */
    public function language()
    {
        return $this->belongsTo(Language::class);
    }
}
