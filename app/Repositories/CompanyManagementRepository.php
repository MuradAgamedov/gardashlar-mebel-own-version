<?php
namespace App\Repositories;

use App\Models\CompanyManagement;
use App\Services\ImageService;
use App\Repositories\Global\BaseRepository;
use Illuminate\Support\Facades\Cache;

class CompanyManagementRepository extends BaseRepository
{
    protected $imageService;

    public function __construct(public CompanyManagement $companyManagement, ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    public function getFirstOrCreate()
    {
        $companyManagement = $this->companyManagement->with(['translates.language'])->first();
        
        if (!$companyManagement) {
            $companyManagement = $this->companyManagement->create();
        }
        
        return $companyManagement;
    }

    public function updateCompanyManagement(array $translations, $languages)
    {
        $companyManagement = $this->companyManagement->first();
        
        if (!$companyManagement) {
            $companyManagement = $this->companyManagement->create();
        }

        foreach ($languages as $index => $language) {
            $translationData = $translations[$index] ?? [];
            
            if (!empty($translationData['content'])) {
                $companyManagement->translates()->updateOrCreate(
                    ['language_id' => $language->id],
                    ['content' => trim($translationData['content'] ?? '')]
                );
            }
        }

        $this->clearCache($languages);

        return $companyManagement;
    }

    public function uploadImage($imageFile)
    {
        $imagePath = $this->imageService->upload($imageFile, 'ckeditor5');
        return asset($imagePath);
    }

    private function clearCache($languages)
    {
        foreach ($languages as $language) {
            Cache::forget("company_management_{$language->id}");
        }
    }
}
