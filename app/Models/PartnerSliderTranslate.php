<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartnerSliderTranslate extends Model
{
    use HasFactory;

    protected $fillable = [
        'partner_slider_id',
        'language_id',
        'title',
    ];

    protected $casts = [
        'partner_slider_id' => 'integer',
        'language_id' => 'integer',
    ];

    /**
     * Get the slider that owns this translation
     */
    public function slider()
    {
        return $this->belongsTo(PartnerSlider::class, 'partner_slider_id');
    }

    /**
     * Get the language for this translation
     */
    public function language()
    {
        return $this->belongsTo(Language::class);
    }
}
