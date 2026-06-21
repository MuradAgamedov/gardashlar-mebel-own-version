<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\PopupAdRepositoryInterface;
use App\Models\PopupAd;
use App\Models\PopupAdTranslate;
use App\Repositories\BaseRepository;
use App\Services\ImageService;
use Illuminate\Support\Facades\DB;

class PopupAdRepository extends BaseRepository implements PopupAdRepositoryInterface
{
    protected ImageService $imageService;

    public function __construct(PopupAd $model, ImageService $imageService)
    {
        parent::__construct($model);
        $this->imageService = $imageService;
    }

    public function getPopupAds($request = null)
    {
        return $this->model
            ->with('translations.language')
            ->orderBy('order')
            ->get();
    }

    public function createPopupAd(array $data, array $translations)
    {
        return DB::transaction(function () use ($data, $translations) {
            // upload image
            $imagePath = $this->imageService->Upload($data['image'], 'popup_ads');

            $popup = $this->model->create([
                'image'  => $imagePath,
                'order'  => $data['order'] ?? 0,
                'status' => $data['status'] ?? 1,
            ]);

            // translations
            foreach ($translations as $trans) {
                PopupAdTranslate::create([
                    'popup_ad_id'   => $popup->id,
                    'language_id'   => $trans['language_id'],
                    'locale'        => $trans['locale'],
                    'title'         => $trans['title'],
                    'description'   => $trans['description'],
                    'image_alt_text'=> $trans['image_alt_text'] ?? null,
                    'link'          => $trans['link'] ?? null,
                ]);
            }

            return $popup->load('translations.language');
        });
    }

    public function updatePopupAd($popup, array $data, array $translations)
    {
        return DB::transaction(function () use ($popup, $data, $translations) {
            // update image
            if (isset($data['image']) && $data['image']) {
                $popup->image = $this->imageService->Update($data['image'], $popup->image, 'popup_ads');
            }

            $popup->order  = $data['order'] ?? $popup->order;
            $popup->status = $data['status'];
            $popup->save();

            foreach ($translations as $trans) {
                $translate = PopupAdTranslate::where('popup_ad_id', $popup->id)
                    ->where('language_id', $trans['language_id'])
                    ->first();

                if ($translate) {
                    $translate->update([
                        'title'         => $trans['title'],
                        'description'   => $trans['description'],
                        'image_alt_text'=> $trans['image_alt_text'] ?? null,
                        'link'          => $trans['link'] ?? null,
                    ]);
                } else {
                    PopupAdTranslate::create([
                        'popup_ad_id'   => $popup->id,
                        'language_id'   => $trans['language_id'],
                        'locale'        => $trans['locale'],
                        'title'         => $trans['title'],
                        'description'   => $trans['description'],
                        'image_alt_text'=> $trans['image_alt_text'] ?? null,
                        'link'          => $trans['link'] ?? null,
                    ]);
                }
            }

            return $popup->load('translations.language');
        });
    }

    public function deletePopupAd($popup)
    {
        return DB::transaction(function () use ($popup) {
            if ($popup->image) {
                $this->imageService->Delete($popup->image);
            }
            return $popup->delete();
        });
    }
}
