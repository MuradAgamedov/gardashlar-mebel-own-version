<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ProjectCategory extends Model
{
    use HasFactory, \App\Traits\Translatable, \App\Traits\HasStatusAndOrder, \App\Traits\HasImage;

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

    /**
     * Scope for active project categories
     */

    /**
     * Scope for ordered project categories
     */

    /**
     * Get the projects for this category
     */
    public function projects()
    {
        return $this->hasMany(Project::class);
    }



    /**
     * Auto-increment order when creating
     */
    
} 