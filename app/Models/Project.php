<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_name',
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
    public function translate()
    {
        return $this->hasOne(ProjectTranslate::class);
    }

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
     * Scope for active projects
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Scope for ordered projects
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    /**
     * Auto-increment order when creating
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($project) {
            if (!isset($project->order)) {
                $maxOrder = static::max('order');
                $project->order = $maxOrder + 1;
            }
        });
    }
} 