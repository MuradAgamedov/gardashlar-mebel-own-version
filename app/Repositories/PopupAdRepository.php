<?php

namespace App\Repositories;

use App\Models\PopupAd;
use App\Models\PopupAdTranslate;
use App\Repositories\Global\BaseRepository;
use App\Services\ImageService;
use Illuminate\Support\Facades\DB;

class PopupAdRepository extends BaseRepository
{
    protected $imageService;

    public function __construct(public PopupAd $popupAd, ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    /**
     * Get all popup ads with translations
     */
    public function getPopupAds($request = null)
    {
        return $this->GetData($this->popupAd, ['translations'])->orderBy('order')->get();
    }

    /**
     * Create new popup ad with translations
     */
    public function createPopupAd(array $data, array $translations)
    {
        return DB::transaction(function () use ($data, $translations) {

            // Upload image
            $imagePath = $this->handleImageUpload($data['image'], 'popup_ads', $this->imageService);

            // Create popup ad
            $popup = $this->popupAd->create([
                'image'  => $imagePath,
                'order'  => $data['order'] ?? 0,
                'status' => $data['status'] ?? true,
            ]);

            // Create translations
            $this->createTranslations($popup, $translations, PopupAdTranslate::class, 'popup_ad_id');

            return $popup->load('translations');
        });
    }

    /**
     * Update popup ad and translations
     */
    public function updatePopupAd(PopupAd $popup, array $data, array $translations)
    {
        return DB::transaction(function () use ($popup, $data, $translations) {

            // Handle image update
            if (isset($data['image'])) {
                $imagePath = $this->handleImageUpdate($popup, $data['image'], 'popup_ads', $this->imageService);
                if ($imagePath) {
                    $popup->image = $imagePath;
                }
            }

            // Update base fields
            $popup->order  = $data['order'] ?? $popup->order;
            $popup->status = $data['status'] ?? $popup->status;
            $popup->save();

            // Update translations
            $this->updateTranslations($popup, $translations, 'popup_ad_id');

            return $popup->load('translations');
        });
    }

    /**
     * Delete popup ad
     */
    public function deletePopupAd(PopupAd $popup)
    {
        return DB::transaction(function () use ($popup) {

            // Delete image file
            $this->handleImageDelete($popup->image, $this->imageService);

            // Delete popup ad
            return $popup->delete();
        });
    }

    /**
     * Reorder popup ads
     */
    public function reorder(array $order)
    {
        return $this->UpdateOrder($this->popupAd, $order);
    }
}
