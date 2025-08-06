<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VacancyTranslate extends Model
{
    use HasFactory;

    protected $fillable = [
        'vacancy_id',
        'language_id',
        'title',
        'slug',
        'position',
        'experience',
        'work_schedule',
        'seo_title',
        'seo_keywords',
        'seo_description',
    ];

    /**
     * Get the vacancy that owns this translation
     */
    public function vacancy()
    {
        return $this->belongsTo(Vacancy::class);
    }

    /**
     * Get the language for this translation
     */
    public function language()
    {
        return $this->belongsTo(Language::class);
    }
}
