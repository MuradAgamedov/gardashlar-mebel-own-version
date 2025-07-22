<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VacancyRequirement extends Model
{
    use HasFactory;

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
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($requirement) {
            // Set order to max order + 1 if not provided
            if (!isset($requirement->order)) {
                $maxOrder = static::max('order');
                $requirement->order = $maxOrder + 1;
            }
        });
    }

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
    public function translate($languageId = null)
    {
        if (!$languageId) {
            $languageId = Language::where('is_default', true)->first()->id ?? 1;
        }
        
        return $this->translates()->where('language_id', $languageId)->first();
    }

    /**
     * Scope for active requirements
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Scope for ordered requirements
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }
}
