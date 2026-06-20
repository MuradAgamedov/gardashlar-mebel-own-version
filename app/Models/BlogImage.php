<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlogImage extends Model
{
    use HasFactory, \App\Traits\HasStatusAndOrder;

    protected $fillable = [
        'blog_post_id',
        'image_url',
        'order',
    ];

    protected $casts = [
        'order' => 'integer',
    ];

    public function blogPost()
    {
        return $this->belongsTo(BlogPost::class);
    }

}
