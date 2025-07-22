<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class IndividualProjectOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'order',
        'status'
    ];

    protected $casts = [
        'status' => 'boolean',
        'order' => 'integer'
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($individualProjectOrder) {
            if (!isset($individualProjectOrder->order)) {
                $maxOrder = static::max('order');
                $individualProjectOrder->order = $maxOrder + 1;
            }
        });
    }

    public function translates(): HasMany
    {
        return $this->hasMany(IndividualProjectOrderTranslate::class);
    }

    public function translate($languageId = null)
    {
        if (!$languageId) {
            $languageId = \App\Models\Language::getMainLanguage()->id;
        }
        
        return $this->translates()->where('language_id', $languageId);
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
