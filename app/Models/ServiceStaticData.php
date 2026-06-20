<?php

namespace App\Models;

use App\Services\ImageService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceStaticData extends Model
{
    use HasFactory, App\Traits\Translatable;

    protected $table = 'service_static_data';
    
    protected $fillable = [
        'card_1_image',
        'card_2_image',
        'card_3_image',
    ];

    protected $appends = [
        'card_1_image_url',
        'card_2_image_url',
        'card_3_image_url',
    ];

    /**
     * Get all translations for this service static data
     */
    public function translates()
    {
        return $this->hasMany(ServiceStaticDataTranslate::class);
    }

    /**
     * Get translation for specific language
     */

    /**
     * Get or create the first service static data record
     */
    public static function getFirstOrCreate()
    {
        $record = static::first();
        if (!$record) {
            $record = static::create();
        }
        return $record;
    }

    /**
     * Get card 1 image URL
     */
    public function getCard1ImageUrlAttribute()
    {
        return $this->getImageUrl($this->card_1_image);
    }

    /**
     * Get card 2 image URL
     */
    public function getCard2ImageUrlAttribute()
    {
        return $this->getImageUrl($this->card_2_image);
    }

    /**
     * Get card 3 image URL
     */
    public function getCard3ImageUrlAttribute()
    {
        return $this->getImageUrl($this->card_3_image);
    }

    /**
     * Helper method to get image URL
     */
    private function getImageUrl($imagePath)
    {
        if (empty($imagePath)) {
            return '';
        }

        $imageService = app(ImageService::class);
        return $imageService->getUrl($imagePath);
    }

    /**
     * Delete image files when model is deleted
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($serviceStaticData) {
            $imageService = app(ImageService::class);
            
            // Delete image files
            if ($serviceStaticData->card_1_image) {
                $imageService->delete($serviceStaticData->card_1_image);
            }
            if ($serviceStaticData->card_2_image) {
                $imageService->delete($serviceStaticData->card_2_image);
            }
            if ($serviceStaticData->card_3_image) {
                $imageService->delete($serviceStaticData->card_3_image);
            }
        });
    }
}
