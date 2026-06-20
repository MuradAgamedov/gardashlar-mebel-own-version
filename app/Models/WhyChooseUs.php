<?php

namespace App\Models;

use App\Services\ImageService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WhyChooseUs extends Model
{
    use HasFactory, \App\Traits\Translatable;

    protected $fillable = [
        'first_icon',
        'second_icon',
        'third_icon',
        'fourth_icon',
    ];

    protected $appends = [
        'first_icon_url',
        'second_icon_url',
        'third_icon_url',
        'fourth_icon_url',
    ];

    /**
     * Get the translations for this WhyChooseUs.
     */
    public function translates()
    {
        return $this->hasMany(WhyChooseUsTranslate::class);
    }

    /**
     * Get translation for specific language
     */

    /**
     * Get first record or create if not exists
     */
    public static function getFirstOrCreate()
    {
        return static::firstOrCreate(['id' => 1]);
    }

    /**
     * Get first icon URL
     */
    public function getFirstIconUrlAttribute()
    {
        return $this->getIconUrl($this->first_icon);
    }

    /**
     * Get second icon URL
     */
    public function getSecondIconUrlAttribute()
    {
        return $this->getIconUrl($this->second_icon);
    }

    /**
     * Get third icon URL
     */
    public function getThirdIconUrlAttribute()
    {
        return $this->getIconUrl($this->third_icon);
    }

    /**
     * Get fourth icon URL
     */
    public function getFourthIconUrlAttribute()
    {
        return $this->getIconUrl($this->fourth_icon);
    }

    /**
     * Get icon URL helper method
     */
    private function getIconUrl($iconPath)
    {
        if (empty($iconPath)) {
            return '';
        }

        $imageService = app(ImageService::class);
        return $imageService->getUrl($iconPath);
    }

    /**
     * Delete icon files when model is deleted
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($whyChooseUs) {
            $imageService = app(ImageService::class);
            
            // Delete icon files
            if ($whyChooseUs->first_icon) {
                $imageService->delete($whyChooseUs->first_icon);
            }
            if ($whyChooseUs->second_icon) {
                $imageService->delete($whyChooseUs->second_icon);
            }
            if ($whyChooseUs->third_icon) {
                $imageService->delete($whyChooseUs->third_icon);
            }
            if ($whyChooseUs->fourth_icon) {
                $imageService->delete($whyChooseUs->fourth_icon);
            }
        });
    }
}
