<?php
namespace App\Repositories;

use App\Models\Product;
use App\Models\ProductType;
use App\Models\HomePageTypeProduct;
use App\Repositories\Global\BaseRepository;

class ProductTypeProductRepository extends BaseRepository
{

    public function __construct(public Product $product)
    {
    }

    // Tip üçün məhsulları gətir
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

    // Tip üçün ana səhifədəki məhsul ID-lərini gətir
    public function getHomePageProductIds($productTypeId)
    {
        return HomePageTypeProduct::where('product_type_id', $productTypeId)
            ->pluck('product_id')
            ->toArray();
    }

    // Tip üçün məhsul axtar (AJAX)
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

    // Tip məlumatını gətir
    public function getProductType($productTypeId)
    {
        return ProductType::findOrFail($productTypeId);
    }

    // Ana səhifədə məhsul statusunu yoxla
    public function checkHomePageStatus($productTypeId, $productId)
    {
        return HomePageTypeProduct::where('product_type_id', $productTypeId)
            ->where('product_id', $productId)
            ->first();
    }
}
