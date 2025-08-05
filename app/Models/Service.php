<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'status',
        'order',
        'slug'
    ];

    protected $casts = [
        'status' => 'boolean',
        'order' => 'integer'
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($service) {
            if (!isset($service->order)) {
                $maxOrder = static::max('order');
                $service->order = $maxOrder + 1;
            }
        });
    }

    public function translates(): HasMany
    {
        return $this->hasMany(ServiceTranslate::class);
    }

    public function translate($languageId = null)
    {
        if (!$languageId) {
            $languageId = Language::getMainLanguage()->id;
        }
        
        return $this->translates()->where('language_id', $languageId)->first();
    }

    public function advantages(): HasMany
    {
        return $this->hasMany(ServiceAdvantage::class);
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