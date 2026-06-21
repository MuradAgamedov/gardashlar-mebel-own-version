<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\CatalogRepositoryInterface;
use App\Models\Catalog;
use App\Repositories\BaseRepository;
use App\Services\ImageService;

class CatalogRepository extends BaseRepository implements CatalogRepositoryInterface
{
    protected ImageService $imageService;

    public function __construct(Catalog $model, ImageService $imageService)
    {
        parent::__construct($model);
        $this->imageService = $imageService;
    }

    public function getCatalogs()
    {
        return $this->GetData($this->catalog, ['translates.language'])->get();
    }

    public function createCatalog(array $data, array $translations, $pdfFile)
    {
        return DB::transaction(function () use ($data, $translations, $pdfFile) {
            if ($pdfFile) {
                $data['catalog_pdf'] = $this->fileService->upload($pdfFile, 'catalogs', [
                    'allowed_types' => ['pdf'],
                    'generate_unique_name' => false,
                ]);
            }

            $catalog = $this->catalog->create($data);

            foreach ($translations as $languageId => $translation) {
                $catalog->translates()->create([
                    'language_id' => $languageId,
                    'catalog_year' => trim($translation['catalog_year'] ?? ''),
                ]);
            }

            return $catalog->load('translates.language');
        });
    }

    public function updateCatalog(Catalog $catalog, array $data, array $translations, $pdfFile = null)
    {
        return DB::transaction(function () use ($catalog, $data, $translations, $pdfFile) {
            if ($pdfFile) {
                if ($catalog->catalog_pdf) {
                    $this->fileService->delete($catalog->catalog_pdf);
                }
                $data['catalog_pdf'] = $this->fileService->upload($pdfFile, 'catalogs', [
                    'allowed_types' => ['pdf'],
                    'generate_unique_name' => false,
                ]);
            }

            $catalog->update($data);

            foreach ($translations as $languageId => $translation) {
                $catalog->translates()->updateOrCreate(
                    ['language_id' => $languageId],
                    ['catalog_year' => trim($translation['catalog_year'] ?? '')]
                );
            }

            return $catalog->load('translates.language');
        });
    }

    public function deleteCatalog(Catalog $catalog)
    {
        return DB::transaction(function () use ($catalog) {
            if ($catalog->catalog_pdf) {
                $this->fileService->delete($catalog->catalog_pdf);
            }
            return $catalog->delete();
        });
    }

    public function reorderCatalogs(array $catalogs)
    {
        return $this->UpdateOrder($this->catalog, $catalogs);
    }
}
