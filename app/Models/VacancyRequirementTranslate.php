<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VacancyRequirementTranslate extends Model
{
    use HasFactory;

    protected $fillable = [
        'vacancy_requirement_id',
        'language_id',
        'title',
        'description',
    ];

    /**
     * Get the requirement that owns this translation
     */
    public function requirement()
    {
        return $this->belongsTo(VacancyRequirement::class, 'vacancy_requirement_id');
    }

    /**
     * Get the language for this translation
     */
    public function language()
    {
        return $this->belongsTo(Language::class);
    }
}
