<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'lang_code',
        'lang_name',
        'order',
        'status',
        'is_main_language',
        'is_default'
    ];

    protected $casts = [
        'status' => 'boolean',
        'is_main_language' => 'boolean',
        'is_default' => 'boolean',
        'order' => 'integer'
    ];

    /**
     * Boot method to set default values
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($language) {
            // Set order to max order + 1 if not provided
            if (!isset($language->order)) {
                $maxOrder = static::max('order');
                $language->order = $maxOrder + 1;
            }
        });
    }

    /**
     * Get the main language
     */
    public static function getMainLanguage()
    {
        return static::where('is_main_language', true)->first();
    }

    /**
     * Get active languages
     */
    public static function getActiveLanguages()
    {
        return static::where('status', true)->orderBy('order')->get();
    }

    /**
     * Scope for active languages
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Scope for main language
     */
    public function scopeMain($query)
    {
        return $query->where('is_main_language', true);
    }
} 