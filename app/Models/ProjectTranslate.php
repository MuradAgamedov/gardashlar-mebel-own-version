<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectTranslate extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'language_id',
        'title',
        'company_name',
        'slug',
        'description',
        'seo_title',
        'seo_keywords',
        'seo_description',
    ];

    /**
     * Get the project that owns this translation
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the language for this translation
     */
    public function language()
    {
        return $this->belongsTo(Language::class);
    }
} 