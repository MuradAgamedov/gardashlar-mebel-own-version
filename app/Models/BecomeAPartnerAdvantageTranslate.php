<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BecomeAPartnerAdvantageTranslate extends Model
{
    use HasFactory;

    protected $fillable = [
        'become_a_partner_advantage_id',
        'language_id',
        'icon_alt_text',
        'title',
        'description',
    ];

    protected $casts = [
        'become_a_partner_advantage_id' => 'integer',
        'language_id' => 'integer',
    ];

    /**
     * Get the advantage that owns this translation
     */
    public function advantage()
    {
        return $this->belongsTo(BecomeAPartnerAdvantage::class, 'become_a_partner_advantage_id');
    }

    /**
     * Get the language for this translation
     */
    public function language()
    {
        return $this->belongsTo(Language::class);
    }
}
