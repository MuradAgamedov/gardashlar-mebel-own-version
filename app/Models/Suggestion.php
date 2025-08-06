<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Suggestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'image',
        'order',
        'status',
        'pdf'
    ];

    protected $casts = [
        'status' => 'boolean',
        'order' => 'integer'
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($suggestion) {
            if (!isset($suggestion->order)) {
                $maxOrder = static::max('order');
                $suggestion->order = $maxOrder + 1;
            }
        });
    }

    public function translates(): HasMany
    {
        return $this->hasMany(SuggestionTranslate::class);
    }

    public function translate($languageId = null)
    {
        if (!$languageId) {
            $languageId = Language::getMainLanguage()->id;
        }
        
        return $this->translates()->where('language_id', $languageId)->first();
    }

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }
}
