<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectCategoryTranslate extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_category_id',
        'language_id',
        'title',
        'slug',
        'seo_title',
        'seo_keywords',
        'seo_description',
        'image_alt_text',
    ];

    /**
     * Get the project category that owns this translation
     */
    public function projectCategory()
    {
        return $this->belongsTo(ProjectCategory::class);
    }

    /**
     * Get the language for this translation
     */
    public function language()
    {
        return $this->belongsTo(Language::class);
    }
} 