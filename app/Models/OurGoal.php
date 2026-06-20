<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OurGoal extends Model
{
    use HasFactory, \App\Traits\Translatable, \App\Traits\HasStatusAndOrder;

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

    /**
     * Scope for active goals
     */

    /**
     * Scope for ordered goals
     */

    /**
     * Auto-increment order when creating
     */
    
}
