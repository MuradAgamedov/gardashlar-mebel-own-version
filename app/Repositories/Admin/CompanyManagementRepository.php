<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\CompanyManagementRepositoryInterface;
use App\Models\CompanyManagement;
use App\Repositories\BaseRepository;
use App\Services\ImageService;
use Illuminate\Support\Facades\Cache;

class CompanyManagementRepository extends BaseRepository implements CompanyManagementRepositoryInterface
{
    protected ImageService $imageService;

    public function __construct(CompanyManagement $model, ImageService $imageService)
    {
        parent::__construct($model);
        $this->imageService = $imageService;
    }

    public function updateCompanyManagement(array $translations, $languages)
    {
        $companyManagement = $this->model->first();
        
        if (!$companyManagement) {
            $companyManagement = $this->model->create([]);
        }

        foreach ($languages as $index => $language) {
            $translationData = $translations[$index] ?? [];

            $companyManagement->translates()->updateOrCreate(
                ['language_id' => $language->id],
                ['content' => trim($translationData['content'] ?? '')]
            );
        }

        $this->clearCache($languages);

        return $companyManagement;
    }

    public function uploadImage($imageFile)
    {
        $imagePath = $this->imageService->upload($imageFile, 'ckeditor5');
        return asset($imagePath);
    }

    protected function clearCache($languages)
    {
        foreach ($languages as $language) {
            Cache::forget("company_management_{$language->id}");
        }
    }
}
