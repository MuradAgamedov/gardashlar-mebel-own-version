<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory, App\Traits\Translatable, App\Traits\HasStatusAndOrder;

    protected $fillable = [
        'project_category_id',
        'order',
        'status',
    ];

    protected $casts = [
        'order' => 'integer',
        'status' => 'boolean',
    ];

    /**
     * Get the translations for this project
     */
    public function translates()
    {
        return $this->hasMany(ProjectTranslate::class);
    }

    /**
     * Get the first translation
     */

    /**
     * Get the project category that owns this project
     */
    public function category()
    {
        return $this->belongsTo(ProjectCategory::class, 'project_category_id');
    }

    /**
     * Get the videos for this project
     */
    public function videos()
    {
        return $this->hasMany(ProjectVideo::class);
    }

    /**
     * Get the images for this project
     */
    public function images()
    {
        return $this->hasMany(ProjectImage::class);
    }

    /**
     * Get the favorites for this project
     */
    public function favorites()
    {
        return $this->hasMany(ProjectFavorite::class);
    }

    /**
     * Scope for active projects
     */

    /**
     * Scope for ordered projects
     */

    /**
     * Auto-increment order when creating
     */
    
} 