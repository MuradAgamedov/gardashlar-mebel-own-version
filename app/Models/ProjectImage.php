<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'image',
        'is_main_image',
        'order',
        'status',
        'project_id'
    ];

    protected $casts = [
        'is_main_image' => 'boolean',
        'status' => 'boolean',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function getImageUrlAttribute()
    {
        return asset('uploads/' . $this->image);
    }
}
