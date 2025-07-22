<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BecomeAInvestorTranslate extends Model
{
    use HasFactory;

    protected $fillable = [
        'become_a_investor_id',
        'language_id',
        'content',
    ];

    protected $casts = [
        'become_a_investor_id' => 'integer',
        'language_id' => 'integer',
    ];

    /**
     * Get the become a investor that owns this translation
     */
    public function becomeAInvestor()
    {
        return $this->belongsTo(BecomeAInvestor::class);
    }

    /**
     * Get the language for this translation
     */
    public function language()
    {
        return $this->belongsTo(Language::class);
    }
}
