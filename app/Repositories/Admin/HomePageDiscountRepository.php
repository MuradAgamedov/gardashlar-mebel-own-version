<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\HomePageDiscountRepositoryInterface;
use App\Models\HomePageDiscount;
use App\Repositories\BaseRepository;
use App\Services\ImageService;

class HomePageDiscountRepository extends BaseRepository implements HomePageDiscountRepositoryInterface
{
    protected ImageService $imageService;

    public function __construct(HomePageDiscount $model, ImageService $imageService)
    {
        parent::__construct($model);
        $this->imageService = $imageService;
    }

    public function updateHomePageDiscount(array $translations, $languages, $bgImageFile = null)
    {
        $homePageDiscount = $this->model->first();
        
        if (!$homePageDiscount) {
            $homePageDiscount = $this->model->create();
        }

        if ($bgImageFile) {
            $imagePath = $this->imageService->update(
                $bgImageFile, 
                $homePageDiscount->bg_image, 
                'home-page-discount'
            );
            $homePageDiscount->update(['bg_image' => $imagePath]);
        }

        foreach ($languages as $language) {
            $translationData = $translations[$language->id] ?? [];
            
            $homePageDiscount->translates()->updateOrCreate(
                ['language_id' => $language->id],
                [
                    'title' => trim($translationData['title'] ?? ''),
                    'subtitle' => trim($translationData['subtitle'] ?? ''),
                    'button_link' => trim($translationData['button_link'] ?? ''),
                ]
            );
        }

        return $homePageDiscount;
    }
}
