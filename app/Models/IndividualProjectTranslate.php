<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IndividualProjectTranslate extends Model
{
    use HasFactory;

    protected $fillable = [
        'individual_project_id',
        'language_id',
        'title',
        'description',
        'seo_title',
        'seo_keywords',
        'seo_description'
    ];

    public function individualProject(): BelongsTo
    {
        return $this->belongsTo(IndividualProject::class);
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
} 