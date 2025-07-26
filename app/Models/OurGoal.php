<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OurGoal extends Model
{
    use HasFactory;

    protected $fillable = [
        'order',
        'status',
    ];

    protected $casts = [
        'order' => 'integer',
        'status' => 'boolean',
    ];

    /**
     * Get the translations for this goal
     */
    public function translates()
    {
        return $this->hasMany(OurGoalTranslate::class);
    }

    /**
     * Get the first translation
     */
    public function translate()
    {
        return $this->hasOne(OurGoalTranslate::class);
    }

    /**
     * Scope for active goals
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Scope for ordered goals
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    /**
     * Auto-increment order when creating
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($ourGoal) {
            if (!isset($ourGoal->order)) {
                $maxOrder = static::max('order');
                $ourGoal->order = $maxOrder + 1;
            }
        });
    }
}
