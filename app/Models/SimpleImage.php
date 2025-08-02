<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SimpleImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'keyword',
        'image_path'
    ];

    protected $hidden = ['created_at', 'updated_at'];

    /**
     * Get the translations for this simple image
     */
    public function translates(): HasMany
    {
        return $this->hasMany(SimpleImageTranslate::class);
    }

    /**
     * Get translation for specific language
     */
    public function translate($languageId = null)
    {
        if (!$languageId) {
            $languageId = Language::getMainLanguage()->id;
        }
        
        return $this->translates()->where('language_id', $languageId)->first();
    }

    /**
     * Get image URL
     */
    public function getImageUrlAttribute()
    {
        if (empty($this->image_path)) {
            return '';
        }
        return asset('storage/' . $this->image_path);
    }

    /**
     * Get image by keyword
     */
    public static function getByKeyword($keyword)
    {
        return static::where('keyword', $keyword)->first();
    }

    /**
     * Get image URL by keyword
     */
    public static function getImageUrl($keyword)
    {
        $image = static::getByKeyword($keyword);
        return $image ? $image->image_url : '';
    }
}
