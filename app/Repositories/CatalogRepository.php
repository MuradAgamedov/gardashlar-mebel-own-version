<?php
namespace App\Repositories;

use App\Models\Catalog;
use App\Services\FileService;
use App\Repositories\Global\BaseRepository;
use Illuminate\Support\Facades\DB;

class CatalogRepository extends BaseRepository
{
    protected $fileService;

    public function __construct(public Catalog $catalog, FileService $fileService)
    {
        $this->fileService = $fileService;
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
