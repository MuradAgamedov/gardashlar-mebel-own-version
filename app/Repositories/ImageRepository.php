<?php
namespace App\Repositories;

use App\Models\Image;
use App\Services\ImageService;
use App\Repositories\Global\BaseRepository;

class ImageRepository extends BaseRepository
{
    public function __construct(
        public Image $image,
        protected ImageService $imageService
    ) {
    }

    public function getImages()
    {
        return $this->image->with('translates.language')->get();
    }

    public function createImage(array $data, array $translations, $imageFile)
    {
        $imagePath = $this->imageService->upload($imageFile, 'images');
        
        $image = $this->image->create([
            'keyword' => $data['keyword'],
            'image' => $imagePath,
        ]);

        foreach ($translations as $languageId => $translation) {
            if (!empty($translation['image_alt_text'])) {
                $image->translates()->create([
                    'language_id' => $languageId,
                    'image_alt_text' => $translation['image_alt_text'],
                ]);
            }
        }

        return $image;
    }

    public function updateImage(Image $image, array $data, array $translations, $imageFile = null)
    {
        $updateData = ['keyword' => $data['keyword']];

        if ($imageFile) {
            if ($image->image) {
                $this->imageService->delete($image->image);
            }
            $updateData['image'] = $this->imageService->upload($imageFile, 'images');
        }

        $image->update($updateData);

        foreach ($translations as $languageId => $translation) {
            if (!empty($translation['image_alt_text'])) {
                $image->translates()->updateOrCreate(
                    ['language_id' => $languageId],
                    ['image_alt_text' => $translation['image_alt_text']]
                );
            }
        }

        return $image;
    }

    public function deleteImage(Image $image)
    {
        if ($image->image) {
            $this->imageService->delete($image->image);
        }
        return $image->delete();
    }
}
