<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HomePageProject extends Model
{
    use HasFactory, \App\Traits\HasStatusAndOrder;

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

    /**
     * Scope for ordered projects
     */
    
}
