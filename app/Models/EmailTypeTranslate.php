<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailTypeTranslate extends Model
{
    use HasFactory;

    protected $fillable = [
        'email_type_id',
        'language_id',
        'title',
    ];

    protected $casts = [
        'email_type_id' => 'integer',
        'language_id' => 'integer',
    ];

    /**
     * Get the email type that owns this translation
     */
    public function emailType()
    {
        return $this->belongsTo(EmailType::class);
    }

    /**
     * Get the language for this translation
     */
    public function language()
    {
        return $this->belongsTo(Language::class);
    }
} 