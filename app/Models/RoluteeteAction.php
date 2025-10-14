<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoluteeteAction extends Model
{
    use HasFactory;

    protected $fillable = [
        'image',
        'percent',
    ];

    public function translates()
    {
        return $this->hasMany(RoluteeteActionTranslate::class, 'roluteete_action_id', 'id');
    }
}
