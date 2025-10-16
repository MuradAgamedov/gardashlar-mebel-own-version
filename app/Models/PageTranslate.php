<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PageTranslate extends Model
{
    protected $fillable = [
        'page_id',
        'language_id',
        'title',
        'slug',
        'seo_title',
        'seo_keywords',
        'seo_description',
        'opengraph_title',
        'opengraph_description',
        'opengraph_image_alt_text'
    ];

    protected $casts = [
        'page_id' => 'integer',
        'language_id' => 'integer'
    ];

    protected $hidden = ['created_at', 'updated_at', 'page_id', 'language_id', 'status'];

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
