<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    use HasFactory, \App\Traits\HasStatusAndOrder;

    protected $fillable = [
        'title',
        'lang_code',
        'lang_name',
        'flag',
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

    protected $hidden = ['created_at', 'updated_at', 'lang_name', 'is_default', 'flag', 'status'];
    /**
     * Boot method to set default values
     */

    protected static ?Language $mainLanguage = null;
    protected static ?\Illuminate\Database\Eloquent\Collection $activeLanguages = null;

    /**
     * Get the main language
     */
    public static function getMainLanguage()
    {
        if (self::$mainLanguage === null) {
            self::$mainLanguage = static::where('is_main_language', true)->first();
        }
        return self::$mainLanguage;
    }

    /**
     * Get active languages
     */
    public static function getActiveLanguages()
    {
        if (self::$activeLanguages === null) {
            self::$activeLanguages = static::where('status', true)->orderBy('order')->get();
        }
        return self::$activeLanguages;
    }

    /**
     * Scope for active languages
     */

    /**
     * Scope for main language
     */
    public function scopeMain($query)
    {
        return $query->where('is_main_language', true);
    }
} 