<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OurGoalTranslate extends Model
{
    use HasFactory;

    protected $table = 'our_goals_translates';

    protected $fillable = [
        'our_goal_id',
        'language_id',
        'title',
        'description',
    ];

    /**
     * Get the goal that owns this translation
     */
    public function ourGoal()
    {
        return $this->belongsTo(OurGoal::class);
    }

    /**
     * Get the language for this translation
     */
    public function language()
    {
        return $this->belongsTo(Language::class);
    }
}
