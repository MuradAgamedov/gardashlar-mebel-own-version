<?php
namespace App\Repositories;

use App\Models\HeroProduct;
use App\Models\Product;
use App\Repositories\Global\BaseRepository;

class HeroProductRepository extends BaseRepository
{

    public function __construct(public HeroProduct $heroProduct)
    {
    }

    // Hero məhsulları gətir
    public function getHeroProducts()
    {
        return $this->heroProduct->with('product.translates.language', 'product.categories.translates.language')
            ->ordered()
            ->get();
    }

    // Məhsul axtarışı (AJAX üçün)
    public function searchProducts($search, $excludeIds = [])
    {
        return Product::with(['translates.language', 'categories.translates.language'])
            ->whereHas('translates', function ($query) use ($search) {
                $query->where('title', 'like', '%' . $search . '%');
            })
            ->whereNotIn('id', $excludeIds)
            ->active()
            ->ordered()
            ->limit(10)
            ->get();
    }
}
