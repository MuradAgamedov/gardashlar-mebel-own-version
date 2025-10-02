<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserRoluteeteAction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'roluteete_action_id',
        'used',
    ];

    // əlaqələr
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function roluteeteAction()
    {
        return $this->belongsTo(RoluteeteAction::class);
    }
}
