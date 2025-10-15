<?php
namespace App\Repositories;

use App\Models\ProductImage;
use App\Models\ProductColor;
use App\Repositories\Global\BaseRepository;

class ProductImageRepository extends BaseRepository
{

    public function __construct(public ProductImage $productImage)
    {
    }

    // Məhsul üçün şəkilləri filtrlə və gətir
    public function getProductImages($product, $request)
    {
        $query = $product->images()->with('color');
        
        // Filter by color
        if ($request->has('color_id') && $request->color_id !== '') {
            if ($request->color_id === 'no-color') {
                $query->whereNull('product_color_id');
            } else {
                $query->where('product_color_id', $request->color_id);
            }
        }
        
        return $query->ordered()->get();
    }

    // Bütün rəngləri gətir
    public function getAllColors()
    {
        return ProductColor::all();
    }
}
