<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuggestionsComplaintsForm extends Model
{
    use HasFactory;

    protected $table = 'suggestions_complaints_forms';

    protected $fillable = [
        'name',
        'mobile',
        'email',
        'file',
        'message',
        'is_read'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
} 