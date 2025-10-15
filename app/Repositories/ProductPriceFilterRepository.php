<?php
namespace App\Repositories;

use App\Models\ProductPriceFilter;
use App\Repositories\Global\BaseRepository;

class ProductPriceFilterRepository extends BaseRepository
{

    public function __construct(public ProductPriceFilter $productPriceFilter)
    {
    }

    // Bütün qiymət filtrlərini gətir
    public function getFilters()
    {
        return $this->productPriceFilter->with('translates.language')->ordered()->get();
    }
}
