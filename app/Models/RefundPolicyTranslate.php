<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RefundPolicyTranslate extends Model
{
    use HasFactory;

    protected $fillable = [
        'refund_policy_id',
        'language_id',
        'content',
    ];

    protected $casts = [
        'refund_policy_id' => 'integer',
        'language_id' => 'integer',
    ];

    /**
     * Get the refund policy that owns this translation
     */
    public function refundPolicy()
    {
        return $this->belongsTo(RefundPolicy::class);
    }

    /**
     * Get the language for this translation
     */
    public function language()
    {
        return $this->belongsTo(Language::class);
    }
}
