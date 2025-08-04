<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdministrationTranslate extends Model
{
    use HasFactory;

    protected $fillable = [
        'administration_id',
        'language_id',
        'title',
        'position'
    ];

    protected $casts = [
        'administration_id' => 'integer',
        'language_id' => 'integer'
    ];

    /**
     * Get the administration that owns this translation
     */
    public function administration()
    {
        return $this->belongsTo(Administration::class);
    }

    /**
     * Get the language for this translation
     */
    public function language()
    {
        return $this->belongsTo(Language::class);
    }
} 