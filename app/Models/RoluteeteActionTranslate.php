<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoluteeteActionTranslate extends Model
{
    use HasFactory;

    protected $fillable = [
        'roluteete_action_id',
        'language_id',
        'title',
        'type',
    ];

    // Əsas əlaqə action-a
    public function action()
    {
        return $this->belongsTo(RoluteeteAction::class, 'roluteete_action_id');
    }

    // Dil modeli ilə əlaqə
    public function language()
    {
        return $this->belongsTo(Language::class, 'language_id');
    }
}
