<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuggestAPlaceTranslate extends Model
{
    use HasFactory;

    protected $fillable = [
        'suggest_a_place_id',
        'language_id',
        'title',
        'description',
        'second_title',
        'second_title_subtitle',
    ];

    protected $casts = [
        'suggest_a_place_id' => 'integer',
        'language_id' => 'integer',
    ];

    /**
     * Get the place that owns this translation
     */
    public function place()
    {
        return $this->belongsTo(SuggestAPlace::class, 'suggest_a_place_id');
    }

    /**
     * Get the language for this translation
     */
    public function language()
    {
        return $this->belongsTo(Language::class);
    }
}
