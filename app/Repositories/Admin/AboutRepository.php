<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\AboutRepositoryInterface;
use App\Models\About;
use App\Models\AboutTranslate;
use App\Repositories\BaseRepository;
use App\Services\ImageService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class AboutRepository extends BaseRepository implements AboutRepositoryInterface
{
    protected ImageService $imageService;

    public function __construct(About $model, ImageService $imageService)
    {
        parent::__construct($model);
        $this->imageService = $imageService;
    }

    public function getAbout(): Collection
    {
        return $this->query()->with(['translates.language'])->ordered()->get();
    }

    public function createAbout(array $data, array $translates, $imageFile): Model
    {
        return DB::transaction(function () use ($data, $translates, $imageFile) {
            $imagePath = $this->handleImageUpload($imageFile, 'abouts', $this->imageService);

            $about = $this->create([
                'image' => $imagePath,
                'date' => $data['date'] ?? null,
                'status' => $data['status'] ?? true
            ]);

            $this->createTranslations($about, $translates, AboutTranslate::class, 'about_id');

            return $about->load('translates.language');
        });
    }

    public function updateAbout(Model $about, array $data, array $translates, $imageFile = null): Model
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

    public function deleteAbout(Model $about): bool
    {
        return DB::transaction(function () use ($about) {
            $this->handleImageDelete($about->image, $this->imageService);
            return $about->delete();
        });
    }

    public function reorderAbouts(array $order): bool
    {
        return $this->reorder($order);
    }
}
