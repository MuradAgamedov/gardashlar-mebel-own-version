<?php
namespace App\Repositories;

use App\Models\ProductTag;
use App\Repositories\Global\BaseRepository;

class ProductTagRepository extends BaseRepository
{

    public function __construct(public ProductTag $productTag)
    {
    }

    // Bütün tag-ları gətir
    public function getTags()
    {
        return $this->productTag->with('translates.language')->ordered()->get();
    }
}
