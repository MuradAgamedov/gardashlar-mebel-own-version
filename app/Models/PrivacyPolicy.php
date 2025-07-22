<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PrivacyPolicy extends Model
{
    use HasFactory;

    protected $fillable = [];

    public function translates(): HasMany
    {
        return $this->hasMany(PrivacyPolicyTranslate::class);
    }

    public function translate($languageId = null)
    {
        if (!$languageId) {
            $languageId = Language::getMainLanguage()->id;
        }
        
        return $this->translates()->where('language_id', $languageId)->first();
    }
} 