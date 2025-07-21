<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestimonialTranslate extends Model
{
    use HasFactory;

    protected $fillable = [
        'testimonial_id',
        'language_id',
        'full_name',
        'position',
        'comment',
    ];

    protected $casts = [
        'testimonial_id' => 'integer',
        'language_id' => 'integer',
    ];

    /**
     * Get the testimonial that owns this translation
     */
    public function testimonial()
    {
        return $this->belongsTo(Testimonial::class);
    }

    /**
     * Get the language for this translation
     */
    public function language()
    {
        return $this->belongsTo(Language::class);
    }
}
