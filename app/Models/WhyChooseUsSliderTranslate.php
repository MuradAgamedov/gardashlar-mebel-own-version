<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WhyChooseUsSliderTranslate extends Model
{
    use HasFactory;

    protected $fillable = [
        'why_choose_us_slider_id',
        'language_id',
    ];

    protected $casts = [
        'why_choose_us_slider_id' => 'integer',
        'language_id' => 'integer',
    ];

    /**
     * Get the slider that owns this translation.
     */
    public function slider()
    {
        return $this->belongsTo(WhyChooseUsSlider::class, 'why_choose_us_slider_id');
    }

    /**
     * Get the language for this translation.
     */
    public function language()
    {
        return $this->belongsTo(Language::class);
    }
}
