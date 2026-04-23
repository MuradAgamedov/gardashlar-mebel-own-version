<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewsCategoryTranslate extends Model
{
    use HasFactory;

    protected $fillable = [
        'news_category_id',
        'language_id',
        'title',
        'slug',
        'seo_title',
        'seo_keywords',
        'seo_description',
    ];

    /**
     * Get the news category that owns the translate
     */
    public function newsCategory()
    {
        return $this->belongsTo(NewsCategory::class);
    }

    /**
     * Get the language that owns the translate
     */
    public function language()
    {
        return $this->belongsTo(Language::class);
    }
}
