<?php
namespace App\Repositories;

use App\Models\HomePageDiscount;
use App\Services\ImageService;
use App\Repositories\Global\BaseRepository;

class HomePageDiscountRepository extends BaseRepository
{
    public function __construct(
        public HomePageDiscount $homePageDiscount,
        protected ImageService $imageService
    ) {
    }

    public function getFirstOrCreate()
    {
        $homePageDiscount = $this->homePageDiscount->with(['translates.language'])->first();
        
        if (!$homePageDiscount) {
            $homePageDiscount = $this->homePageDiscount->create();
        }
        
        return $homePageDiscount;
    }

    public function updateHomePageDiscount(array $translations, $languages, $bgImageFile = null)
    {
        $homePageDiscount = $this->homePageDiscount->first();
        
        if (!$homePageDiscount) {
            $homePageDiscount = $this->homePageDiscount->create();
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
