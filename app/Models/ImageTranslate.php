<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImageTranslate extends Model
{
    use HasFactory;

    protected $table = 'image_translates';

    protected $fillable = [
        'image_id',
        'language_id',
        'image_alt_text',
    ];

    /**
     * Get the image that owns this translation
     */
    public function image()
    {
        return $this->belongsTo(Image::class);
    }

    /**
     * Get the language for this translation
     */
    public function language()
    {
        return $this->belongsTo(Language::class);
    }
}
