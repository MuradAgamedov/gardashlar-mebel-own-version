<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vacancy extends Model
{
    use HasFactory;

    protected $fillable = [
        'last_apply_date',
        'email',
        'address',
        'order',
        'status',
        'phone',
        'age'
    ];
        // App\Models\Vacancy.php
        public function scopeNotExpired($query)
        {
            return $query->where(function ($q) {
                $q->whereNull('last_apply_date')
                ->orWhere('last_apply_date', '>=', now());
            });
        }

    protected $casts = [
        'last_apply_date' => 'date',
        'order' => 'integer',
        'status' => 'boolean',
        'age' => 'integer',
    ];

    /**
     * Boot method to set default values
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($vacancy) {
            // Set order to max order + 1 if not provided
            if (!isset($vacancy->order)) {
                $maxOrder = static::max('order');
                $vacancy->order = $maxOrder + 1;
            }
        });
    }

    /**
     * Get all translations for this vacancy
     */
    public function translates()
    {
        return $this->hasMany(VacancyTranslate::class);
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
     * Get all requirements for this vacancy
     */
    public function requirements()
    {
        return $this->hasMany(VacancyRequirement::class);
    }

/**
     * Get all forms for this vacancy
     */
    public function forms()
    {
        return $this->hasMany(VacancyForm::class);
    }

    /**
     * Scope for active vacancies
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Scope for vacancies that haven't expired
     */
  

    /**
     * Scope for ordered vacancies
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }



    /**
     * Check if vacancy is expired
     */
    public function getIsExpiredAttribute()
    {
        return $this->last_apply_date < now()->toDateString();
    }
}
