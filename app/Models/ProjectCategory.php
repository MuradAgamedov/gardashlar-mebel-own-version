<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ProjectCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'order',
        'status',
        'image',
    ];

    protected $casts = [
        'order' => 'integer',
        'status' => 'boolean',
    ];

    protected $appends = ['image_url'];

    /**
     * Get the translations for this project category
     */
    public function translates()
    {
        return $this->hasMany(ProjectCategoryTranslate::class);
    }

    /**
     * Get the first translation
     */
    public function translate()
    {
        return $this->hasOne(ProjectCategoryTranslate::class);
    }

    /**
     * Scope for active project categories
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Scope for ordered project categories
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    /**
     * Get the projects for this category
     */
    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    /**
     * Get the full URL for the image
     */
    public function getImageUrlAttribute()
    {
        return $this->image ? env('APP_URL') . Storage::url($this->image) : null;
    }

    /**
     * Auto-increment order when creating
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($projectCategory) {
            if (!isset($projectCategory->order)) {
                $maxOrder = static::max('order');
                $projectCategory->order = $maxOrder + 1;
            }
        });
    }
} 