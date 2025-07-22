<?php

namespace App\Models;

use App\Services\ImageService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceStaticData extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_id',
        'card_1_image',
        'card_2_image',
        'card_3_image'
    ];

    protected $casts = [
        'service_id' => 'integer'
    ];

    protected $appends = [
        'card_1_image_url',
        'card_2_image_url',
        'card_3_image_url'
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function translates(): HasMany
    {
        return $this->hasMany(ServiceStaticDataTranslate::class);
    }

    public function translate($languageId = null)
    {
        if (!$languageId) {
            $languageId = Language::getMainLanguage()->id;
        }
        
        return $this->translates()->where('language_id', $languageId)->first();
    }

    public function getCard1ImageUrlAttribute()
    {
        return $this->getImageUrl($this->card_1_image);
    }

    public function getCard2ImageUrlAttribute()
    {
        return $this->getImageUrl($this->card_2_image);
    }

    public function getCard3ImageUrlAttribute()
    {
        return $this->getImageUrl($this->card_3_image);
    }

    private function getImageUrl($imagePath)
    {
        if (empty($imagePath)) {
            return '';
        }

        $imageService = app(ImageService::class);
        return $imageService->getUrl($imagePath);
    }
}
