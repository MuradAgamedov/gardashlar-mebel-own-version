<?php
namespace App\Repositories;

use App\Models\Administration;
use App\Models\AdministrationTranslate;
use App\Repositories\Global\BaseRepository;
use App\Services\ImageService;
use Illuminate\Support\Facades\DB;

class AdministrationRepository extends BaseRepository
{
    protected $imageService;

    public function __construct(public Administration $administration, ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    public function getAdministrations()
    {
        return $this->GetData($this->administration, ['translates.language'])->get();
    }

    public function createAdministration(array $data, array $translates, $imageFile)
    {
        return DB::transaction(function () use ($data, $translates, $imageFile) {
            $imagePath = $this->handleImageUpload($imageFile, 'administrations', $this->imageService);

            $administration = $this->administration->create([
                'image' => $imagePath,
                'status' => $data['status'] ?? true
            ]);

            $this->createTranslations($administration, $translates, AdministrationTranslate::class, 'administration_id');

            return $administration->load('translates.language');
        });
    }

    public function updateAdministration(Administration $administration, array $data, array $translates, $imageFile = null)
    {
        return DB::transaction(function () use ($administration, $data, $translates, $imageFile) {
            $imagePath = $this->handleImageUpdate($administration, $imageFile, 'administrations', $this->imageService);
            
            if ($imagePath) {
                $administration->image = $imagePath;
            }

            $administration->status = $data['status'] ?? true;
            $administration->save();

            $this->updateTranslations($administration, $translates, 'administration_id');

            return $administration->load('translates.language');
        });
    }

    public function deleteAdministration(Administration $administration)
    {
        return DB::transaction(function () use ($administration) {
            $this->handleImageDelete($administration->image, $this->imageService);
            return $administration->delete();
        });
    }

    public function reorderAdministrations(array $order)
    {
        return $this->UpdateOrder($this->administration, $order);
    }
}
