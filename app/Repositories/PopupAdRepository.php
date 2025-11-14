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

    public function getPopupAds($request = null)
    {
        return $this->GetData($this->popupAd, ['translations.language'])
                    ->orderBy('order')
                    ->get();
    }

    public function createPopupAd(array $data, array $translations)
    {
        return DB::transaction(function () use ($data, $translations) {

            $imagePath = $this->handleImageUpload($data['image'], 'popup_ads', $this->imageService);

            $popup = $this->popupAd->create([
                'image'  => $imagePath,
                'order'  => $data['order'] ?? 0,
                'status' => $data['status'] ?? true,
            ]);

            foreach ($translations as $trans) {
                PopupAdTranslate::create([
                    'popup_ad_id'   => $popup->id,
                    'language_id'   => $trans['language_id'],
                    'locale'        => $trans['locale'],
                    'title'         => $trans['title'],
                    'description'   => $trans['description'],
                    'image_alt_text'=> $trans['image_alt_text'] ?? null,
                ]);
            }

            return $popup->load('translations.language');
        });
    }

    public function updatePopupAd(PopupAd $popup, array $data, array $translations)
    {
        return DB::transaction(function () use ($popup, $data, $translations) {

            if (isset($data['image'])) {
                $imagePath = $this->handleImageUpdate($popup, $data['image'], 'popup_ads', $this->imageService);

                if ($imagePath) {
                    $popup->image = $imagePath;
                }
            }

            $popup->order  = $data['order'] ?? $popup->order;
            $popup->status = $data['status'] ?? $popup->status;
            $popup->save();

            foreach ($translations as $trans) {

                $translate = PopupAdTranslate::where('popup_ad_id', $popup->id)
                    ->where('language_id', $trans['language_id'])
                    ->first();

                if ($translate) {
                    // UPDATE
                    $translate->update([
                        'title'         => $trans['title'],
                        'description'   => $trans['description'],
                        'image_alt_text'=> $trans['image_alt_text'] ?? null,
                    ]);
                } else {
                    // CREATE (əgər yox idisə)
                    PopupAdTranslate::create([
                        'popup_ad_id'   => $popup->id,
                        'language_id'   => $trans['language_id'],
                        'locale'        => $trans['locale'],
                        'title'         => $trans['title'],
                        'description'   => $trans['description'],
                        'image_alt_text'=> $trans['image_alt_text'] ?? null,
                    ]);
                }
            }

            return $popup->load('translations.language');
        });
    }

    public function deletePopupAd(PopupAd $popup)
    {
        return DB::transaction(function () use ($popup) {

            $this->handleImageDelete($popup->image, $this->imageService);

            return $popup->delete();
        });
    }

    public function reorder(array $order)
    {
        return $this->UpdateOrder($this->popupAd, $order);
    }
}
