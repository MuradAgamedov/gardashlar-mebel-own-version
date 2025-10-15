<?php
namespace App\Repositories;

use App\Models\About;
use App\Models\AboutTranslate;
use App\Repositories\Global\BaseRepository;
use App\Services\ImageService;
use Illuminate\Support\Facades\DB;

class AboutRepository extends BaseRepository
{
    protected $imageService;

    public function __construct(public About $about, ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    public function getAbout()
    {
        return $this->GetData($this->about, ['translates.language'])->get();
    }

    public function createAbout(array $data, array $translates, $imageFile)
    {
        return DB::transaction(function () use ($data, $translates, $imageFile) {
            $imagePath = $this->handleImageUpload($imageFile, 'abouts', $this->imageService);

            $about = $this->about->create([
                'image' => $imagePath,
                'date' => $data['date'] ?? null,
                'status' => $data['status'] ?? true
            ]);

            $this->createTranslations($about, $translates, AboutTranslate::class, 'about_id');

            return $about->load('translates.language');
        });
    }

    public function updateAbout(About $about, array $data, array $translates, $imageFile = null)
    {
        return DB::transaction(function () use ($about, $data, $translates, $imageFile) {
            $imagePath = $this->handleImageUpdate($about, $imageFile, 'abouts', $this->imageService);
            
            if ($imagePath) {
                $about->image = $imagePath;
            }

            $about->date = $data['date'] ?? null;
            $about->status = $data['status'] ?? true;
            $about->save();

            $this->updateTranslations($about, $translates, 'about_id');

            return $about->load('translates.language');
        });
    }

    public function deleteAbout(About $about)
    {
        return DB::transaction(function () use ($about) {
            $this->handleImageDelete($about->image, $this->imageService);
            return $about->delete();
        });
    }

    public function reorderAbouts(array $order)
    {
        return $this->UpdateOrder($this->about, $order);
    }
}
