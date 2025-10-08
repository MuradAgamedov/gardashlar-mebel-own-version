<?php
namespace App\Repositories;

use App\Models\Catalog;
use App\Repositories\Global\BaseRepository;

class CatalogRepository extends BaseRepository
{

    public function __construct(public Catalog $catalog)
    {
    }

    // məsələn bütün məlumatları gətirmək üçün
    public function getCatalogs()
    {
        return $this->GetData($this->catalog, ['translates.language'])->get();
    }
}
