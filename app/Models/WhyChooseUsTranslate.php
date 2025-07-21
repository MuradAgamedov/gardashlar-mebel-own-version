<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WhyChooseUsTranslate extends Model
{
    protected $fillable = [
        'why_choose_us_id',
        'language_id',
        'first_icon_alt_text',
        'first_title',
        'first_description',
        'second_icon_alt_text',
        'second_title',
        'second_description',
        'third_icon_alt_text',
        'third_title',
        'third_description',
        'fourth_icon_alt_text',
        'fourth_title',
        'fourth_description'
    ];

    protected $casts = [
        'why_choose_us_id' => 'integer',
        'language_id' => 'integer'
    ];

    public function whyChooseUs(): BelongsTo
    {
        return $this->belongsTo(WhyChooseUs::class);
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
