<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\ProductTypeProductRepositoryInterface;
use App\Models\HomePageTypeProduct;
use App\Repositories\BaseRepository;
use App\Services\ImageService;

class ProductTypeProductRepository extends BaseRepository implements ProductTypeProductRepositoryInterface
{
    protected ImageService $imageService;

    public function __construct(HomePageTypeProduct $model, ImageService $imageService)
    {
        parent::__construct($model);
        $this->imageService = $imageService;
    }

    public function getProductsByType($productTypeId, $request)
    {
        $query = $this->product->whereHas('types', function($q) use ($productTypeId) {
            $q->where('product_types.id', $productTypeId);
        })->with(['types', 'categories', 'translates']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('translates', function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        return $query->orderBy('order')->paginate(20);
    }

    public function getHomePageProductIds($productTypeId)
    {
        return HomePageTypeProduct::where('product_type_id', $productTypeId)
            ->pluck('product_id')
            ->toArray();
    }

    public function searchProductsByType($productTypeId, $search)
    {
        return $this->product->whereHas('types', function($q) use ($productTypeId) {
            $q->where('product_types.id', $productTypeId);
        })
        ->whereHas('translates', function($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%");
        })
        ->with(['translates', 'types', 'categories'])
        ->orderBy('order')
        ->limit(10)
        ->get();
    }

    public function getProductType($productTypeId)
    {
        return ProductType::findOrFail($productTypeId);
    }

    public function checkHomePageStatus($productTypeId, $productId)
    {
        return HomePageTypeProduct::where('product_type_id', $productTypeId)
            ->where('product_id', $productId)
            ->first();
    }
}
