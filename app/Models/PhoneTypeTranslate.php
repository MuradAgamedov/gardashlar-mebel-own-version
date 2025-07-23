<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhoneTypeTranslate extends Model
{
    use HasFactory;

    protected $fillable = [
        'phone_type_id',
        'language_id',
        'title',
    ];

    protected $casts = [
        'phone_type_id' => 'integer',
        'language_id' => 'integer',
    ];

    /**
     * Get the phone type that owns this translation
     */
    public function phoneType()
    {
        return $this->belongsTo(PhoneType::class);
    }

    /**
     * Get the language for this translation
     */
    public function language()
    {
        return $this->belongsTo(Language::class);
    }
} 