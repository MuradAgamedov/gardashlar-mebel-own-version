<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteUsageRulesTranslate extends Model
{
    use HasFactory;

    protected $fillable = [
        'site_usage_rules_id',
        'language_id',
        'content',
    ];

    protected $casts = [
        'site_usage_rules_id' => 'integer',
        'language_id' => 'integer',
    ];

    /**
     * Get the site usage rules that owns this translation
     */
    public function siteUsageRules()
    {
        return $this->belongsTo(SiteUsageRules::class);
    }

    /**
     * Get the language for this translation
     */
    public function language()
    {
        return $this->belongsTo(Language::class);
    }
}
