<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BecomeAPartnerTranslate extends Model
{
    use HasFactory;

    protected $fillable = [
        'become_a_partner_id',
        'language_id',
        'content',
    ];

    protected $casts = [
        'become_a_partner_id' => 'integer',
        'language_id' => 'integer',
    ];

    /**
     * Get the become a partner that owns this translation
     */
    public function becomeAPartner()
    {
        return $this->belongsTo(BecomeAPartner::class);
    }

    /**
     * Get the language for this translation
     */
    public function language()
    {
        return $this->belongsTo(Language::class);
    }
}
