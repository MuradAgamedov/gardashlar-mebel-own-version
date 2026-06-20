<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    use HasFactory, App\Traits\Translatable;

    protected $fillable = [
        'keyword',
        'image',
    ];

    /**
     * Get the translations for this image
     */
    public function translates()
    {
        return $this->hasMany(ImageTranslate::class);
    }

    /**
     * Get the first translation
     */
    
}
