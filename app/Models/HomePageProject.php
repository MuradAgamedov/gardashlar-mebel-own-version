<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HomePageProject extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'project_category_id',
        'order',
        'status'
    ];

    protected $casts = [
        'order' => 'integer',
        'status' => 'boolean',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function projectCategory()
    {
        return $this->belongsTo(ProjectCategory::class);
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
}
