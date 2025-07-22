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
        'title',
        'content',
    ];

    public function refundPolicy()
    {
        return $this->belongsTo(RefundPolicy::class);
    }

    public function language()
    {
        return $this->belongsTo(Language::class);
    }
}
