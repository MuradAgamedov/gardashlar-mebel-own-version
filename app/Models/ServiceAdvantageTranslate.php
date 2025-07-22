<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceAdvantageTranslate extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_advantage_id',
        'language_id',
        'title',
        'icon_alt_text',
        'description'
    ];

    protected $casts = [
        'service_advantage_id' => 'integer',
        'language_id' => 'integer'
    ];

    public function serviceAdvantage(): BelongsTo
    {
        return $this->belongsTo(ServiceAdvantage::class);
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
} 