<?php

namespace App\Interfaces\Repositories\Admin;

use App\Interfaces\Repositories\BaseRepositoryInterface;

interface CatalogRepositoryInterface extends BaseRepositoryInterface
{
    //

    public function getCatalogs();

    public function createCatalog(array $data, array $translations, $pdfFile);

    public function updateCatalog(\App\Models\Catalog $catalog, array $data, array $translations, $pdfFile = null);

    public function deleteCatalog(\App\Models\Catalog $catalog);

    public function reorderCatalogs(array $catalogs);
}
