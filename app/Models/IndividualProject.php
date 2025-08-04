<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IndividualProject extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_category_id',
        'image',
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
        
        static::creating(function ($individualProject) {
            if (!isset($individualProject->order)) {
                $maxOrder = static::max('order');
                $individualProject->order = $maxOrder + 1;
            }
        });
    }

    public function translates(): HasMany
    {
        return $this->hasMany(IndividualProjectTranslate::class);
    }

    public function translate($languageId = null)
    {
        if (!$languageId) {
            $languageId = \App\Models\Language::getMainLanguage()->id;
        }
        
        return $this->translates()->where('language_id', $languageId);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProjectCategory::class, 'project_category_id');
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