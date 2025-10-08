<?php
namespace App\Repositories;

use App\Models\KivCategory;
use App\Repositories\Global\BaseRepository;

class KivCategoryRepository extends BaseRepository
{

    public function __construct(public KivCategory $kivCategory)
    {
    }

    // KIV kateqoriyalarını gətir
    public function getCategories()
    {
        return $this->GetData($this->kivCategory, ['translates.language'])->get();
    }
}
