<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\ProductImageRepositoryInterface;
use App\Models\ProductImage;
use App\Repositories\BaseRepository;
use App\Services\ImageService;

class ProductImageRepository extends BaseRepository implements ProductImageRepositoryInterface
{
    protected ImageService $imageService;

    public function __construct(ProductImage $model, ImageService $imageService)
    {
        parent::__construct($model);
        $this->imageService = $imageService;
    }

    public function getProductImages($product, $request)
    {
        $query = $product->images()->with(['color', 'translates.language']);
        
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

    public function getAllColors()
    {
        return ProductColor::all();
    }
}
