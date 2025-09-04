<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceTranslate extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_id',
        'language_id',
        'title',
        'slug',
        'card_description',
        'main_description',
        'seo_title',
        'seo_keywords',
        'seo_description',
        'form_top_text',
        'advantages_title'
    ];

    protected $casts = [
        'service_id' => 'integer',
        'language_id' => 'integer'
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
} 