<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ProjectImage extends Model
{
    use HasFactory, \App\Traits\HasImage;

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


}
