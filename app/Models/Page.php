<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Page extends Model
{
    protected $fillable = [
        'status',
        'order',
        'keyword'
    ];

    protected $casts = [
        'status' => 'boolean',
        'order' => 'integer'
    ];

    protected $hidden = ['created_at', 'updated_at', 'order', 'status', 'keyword'];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($page) {
            if (!isset($page->order)) {
                $maxOrder = static::max('order');
                $page->order = $maxOrder + 1;
            }
        });
    }

    public function translates(): HasMany
    {
        return $this->hasMany(PageTranslate::class);
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
}
