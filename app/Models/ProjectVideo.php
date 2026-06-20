<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectVideo extends Model
{
    use HasFactory, App\Traits\HasStatusAndOrder;

    protected $fillable = [
        'project_id',
        'video_link',
        'order',
        'status',
    ];

    protected $casts = [
        'project_id' => 'integer',
        'order' => 'integer',
        'status' => 'boolean',
    ];

    /**
     * Get the project that owns this video
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Scope for active videos
     */

    /**
     * Scope for ordered videos
     */

    /**
     * Auto-increment order when creating
     */
    
} 