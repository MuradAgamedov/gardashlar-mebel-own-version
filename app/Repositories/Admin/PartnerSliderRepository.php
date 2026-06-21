<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\PartnerSliderRepositoryInterface;
use App\Models\PartnerSlider;
use App\Repositories\BaseRepository;
use App\Services\ImageService;
use Illuminate\Support\Facades\DB;

class PartnerSliderRepository extends BaseRepository implements PartnerSliderRepositoryInterface
{
    protected ImageService $imageService;

    public function __construct(PartnerSlider $model, ImageService $imageService)
    {
        parent::__construct($model);
        $this->imageService = $imageService;
    }

    public function getPartnerSliders()
    {
        return $this->partnerSlider->with('translates.language')->orderBy('order')->get();
    }

    public function createPartnerSlider(array $data, array $translations, $imageFile, $imageService, $languages)
    {
        return DB::transaction(function () use ($data, $translations, $imageFile, $imageService, $languages) {
            $imagePath = null;
            if ($imageFile) {
                $imagePath = $imageService->upload($imageFile, 'partner-sliders', [
                    'max_size' => 2048,
                    'allowed_types' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
                ]);

                if (!$imagePath) {
                    throw new \Exception('Failed to upload image.');
                }
            }

            $slider = $this->partnerSlider->create([
                'image' => $imagePath,
                'status' => $data['status'],
                'link' => $data['link'],
            ]);

            foreach ($languages as $language) {
                $translation = $translations[$language->id] ?? [];
                $slider->translates()->create([
                    'language_id' => $language->id,
                    'title' => trim($translation['title'] ?? ''),
                ]);
            }

            return $slider;
        });
    }

    public function updatePartnerSlider(PartnerSlider $partnerSlider, array $data, array $translations, $imageFile, $imageService, $languages)
    {
        return DB::transaction(function () use ($partnerSlider, $data, $translations, $imageFile, $imageService, $languages) {
            $updateData = [
                'status' => $data['status'],
                'link' => $data['link'],
            ];

            if ($imageFile) {
                $uploaded = $imageService->update($imageFile, $partnerSlider->image, 'partner-sliders', [
                    'max_size' => 2048,
                    'allowed_types' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
                ]);

                if (!$uploaded) {
                    throw new \Exception('Failed to upload image.');
                }

                $updateData['image'] = $uploaded;
            }

            $partnerSlider->update($updateData);

            foreach ($languages as $language) {
                $translation = $translations[$language->id] ?? [];
                $partnerSlider->translates()->updateOrCreate(
                    ['language_id' => $language->id],
                    ['title' => trim($translation['title'] ?? '')]
                );
            }

            return $partnerSlider;
        });
    }

    public function deletePartnerSlider(PartnerSlider $partnerSlider)
    {
        return $partnerSlider->delete();
    }

    public function reorderPartnerSliders(array $sliders)
    {
        foreach ($sliders as $index => $sliderId) {
            $this->partnerSlider->where('id', $sliderId)->update(['order' => $index + 1]);
        }
        return true;
    }
}
