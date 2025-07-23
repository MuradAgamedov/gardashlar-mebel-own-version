<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectVideo extends Model
{
    use HasFactory;

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
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Scope for ordered videos
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

        static::creating(function ($video) {
            if (!isset($video->order)) {
                $maxOrder = static::where('project_id', $video->project_id)->max('order');
                $video->order = $maxOrder + 1;
            }
        });
    }
} 