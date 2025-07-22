<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AboutTranslate extends Model
{
    use HasFactory;

    protected $fillable = [
        'about_id',
        'language_id',
        'title',
        'description',
        'image_alt_text'
    ];

    public function about(): BelongsTo
    {
        return $this->belongsTo(About::class);
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
} 