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
        'seo_description'
    ];

    protected $casts = [
        'page_id' => 'integer',
        'language_id' => 'integer'
    ];

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
