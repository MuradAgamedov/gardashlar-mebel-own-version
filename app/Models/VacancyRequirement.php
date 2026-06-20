<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VacancyRequirement extends Model
{
    use HasFactory, App\Traits\Translatable, App\Traits\HasStatusAndOrder;

    protected $fillable = [
        'vacancy_id',
        'order',
        'status',
    ];

    protected $casts = [
        'order' => 'integer',
        'status' => 'boolean',
    ];

    /**
     * Boot method to set default values
     */

    /**
     * Get the vacancy that owns this requirement
     */
    public function vacancy()
    {
        return $this->belongsTo(Vacancy::class);
    }

    /**
     * Get all translations for this requirement
     */
    public function translates()
    {
        return $this->hasMany(VacancyRequirementTranslate::class);
    }

    /**
     * Get translation for specific language
     */

    /**
     * Scope for active requirements
     */

    /**
     * Scope for ordered requirements
     */
    
}
