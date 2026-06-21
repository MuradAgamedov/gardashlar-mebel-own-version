<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\HeroProductRepositoryInterface;
use App\Models\HeroProduct;
use App\Repositories\BaseRepository;
use App\Services\ImageService;

class HeroProductRepository extends BaseRepository implements HeroProductRepositoryInterface
{
    protected ImageService $imageService;

    public function __construct(HeroProduct $model, ImageService $imageService)
    {
        parent::__construct($model);
        $this->imageService = $imageService;
    }

    public function getHeroProducts()
    {
        return $this->heroProduct->with('product.translates.language', 'product.categories.translates.language')
            ->ordered()
            ->get();
    }

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

    public function createHeroProduct(array $data)
    {
        return $this->heroProduct->create($data);
    }

    public function updateHeroProduct(HeroProduct $heroProduct, array $data)
    {
        $heroProduct->update($data);
        return $heroProduct;
    }

    public function deleteHeroProduct(HeroProduct $heroProduct)
    {
        return $heroProduct->delete();
    }

    public function reorderHeroProducts(array $items)
    {
        foreach ($items as $index => $id) {
            $this->heroProduct->where('id', $id)->update(['order' => $index + 1]);
        }
        return true;
    }

    public function formatSearchResults($products)
    {
        $results = [];
        foreach ($products as $product) {
            $firstTranslation = $product->translates->first();
            $firstCategory = $product->categories->first();
            $categoryTranslation = $firstCategory ? $firstCategory->translates->first() : null;
            
            $results[] = [
                'id' => $product->id,
                'title' => $firstTranslation ? $firstTranslation->title : 'No title',
                'category' => $categoryTranslation ? $categoryTranslation->title : 'No category',
                'text' => ($firstTranslation ? $firstTranslation->title : 'No title') . 
                         ' - ' . ($categoryTranslation ? $categoryTranslation->title : 'No category')
            ];
        }
        return $results;
    }
}
