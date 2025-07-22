<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrivacyPolicyTranslate extends Model
{
    use HasFactory;

    protected $fillable = [
        'privacy_policy_id',
        'language_id',
        'content'
    ];

    public function privacyPolicy(): BelongsTo
    {
        return $this->belongsTo(PrivacyPolicy::class);
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
} 