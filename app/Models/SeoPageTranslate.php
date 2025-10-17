<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SeoPageTranslate extends Model
{
    protected $fillable = [
        'seo_page_id',
        'language_id',
        'slug',
        'title',
        'page_footer',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ];

    protected $casts = [
        'seo_page_id' => 'integer',
        'language_id' => 'integer',
    ];

    protected $hidden = ['created_at', 'updated_at', 'seo_page_id', 'language_id'];

    public function seoPage(): BelongsTo
    {
        return $this->belongsTo(SeoPage::class);
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
