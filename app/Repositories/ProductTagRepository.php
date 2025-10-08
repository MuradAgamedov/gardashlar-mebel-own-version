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

    // Order-i yenilə
    public function updateOrder($id, $order)
    {
        return $this->productTag->where('id', $id)->update(['order' => $order]);
    }
}
