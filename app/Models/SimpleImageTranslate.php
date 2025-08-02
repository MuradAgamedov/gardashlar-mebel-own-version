<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SimpleImageTranslate extends Model
{
    use HasFactory;

    protected $fillable = [
        'simple_image_id',
        'language_id',
        'image_go_url'
    ];

    protected $hidden = ['created_at', 'updated_at'];

    /**
     * Get the simple image that owns this translation
     */
    public function simpleImage()
    {
        return $this->belongsTo(SimpleImage::class);
    }

    /**
     * Get the language for this translation
     */
    public function language()
    {
        return $this->belongsTo(Language::class);
    }
}
