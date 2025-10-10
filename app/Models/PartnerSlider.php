<?php

namespace App\Models;

use App\Services\ImageService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartnerSlider extends Model
{
    use HasFactory;

    protected $fillable = [
        'order',
        'status',
        'image',
        'link', // 🔥 yeni əlavə edildi
    ];

    protected $casts = [
        'order' => 'integer',
        'status' => 'boolean',
    ];

    protected $appends = [
        'image_url',
    ];

    /**
     * Get all translations for this slider
     */
    public function translates()
    {
        return $this->hasMany(PartnerSliderTranslate::class);
    }

    /**
     * Get translation for specific language
     */
    public function translate($languageId = null)
    {
        if (!$languageId) {
            $languageId = Language::where('is_default', true)->first()->id ?? 1;
        }

        return $this->translates()->where('language_id', $languageId)->first();
    }

    /**
     * Scope for active sliders
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Scope for ordered sliders
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    /**
     * Get image URL
     */
    public function getImageUrlAttribute()
    {
        return $this->getImageUrl($this->image);
    }

    /**
     * Helper method to get image URL
     */
    private function getImageUrl($imagePath)
    {
        if (!$imagePath) {
            return null;
        }

        $imageService = app(ImageService::class);
        return $imageService->getUrl($imagePath);
    }

    /**
     * Boot method for auto-incrementing order and file cleanup
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-increment order on creation
        static::creating(function ($slider) {
            if (!$slider->order) {
                $maxOrder = static::max('order');
                $slider->order = $maxOrder + 1;
            }
        });

        // Delete associated files when slider is deleted
        static::deleting(function ($slider) {
            $imageService = app(ImageService::class);
            
            if ($slider->image) {
                $imageService->delete($slider->image);
            }
        });
    }
}
