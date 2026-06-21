<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\ProductColorRepositoryInterface;
use App\Models\ProductColor;
use App\Repositories\BaseRepository;
use App\Services\ImageService;

class ProductColorRepository extends BaseRepository implements ProductColorRepositoryInterface
{
    protected ImageService $imageService;

    public function __construct(ProductColor $model, ImageService $imageService)
    {
        parent::__construct($model);
        $this->imageService = $imageService;
    }

    public function getProductColors($request)
    {
        $query = $this->productColor->query();
        
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('color_code', 'like', "%{$search}%");
        }
        
        return $query->orderBy('created_at', 'desc')->paginate(30);
    }

    public function createProductColor($colorCode)
    {
        if (!preg_match('/^(#[0-9A-F]{6}|rgba?\([^)]+\))$/i', $colorCode)) {
            throw new \Exception('Invalid color format. Use hex (#FF0000) or rgba (rgba(255,0,0,0.5)) format.');
        }

        return $this->productColor->create(['color_code' => $colorCode]);
    }

    public function updateProductColor(ProductColor $productColor, $colorCode)
    {
        if (!preg_match('/^(#[0-9A-F]{6}|rgba?\([^)]+\))$/i', $colorCode)) {
            throw new \Exception('Invalid color format. Use hex (#FF0000) or rgba (rgba(255,0,0,0.5)) format.');
        }

        $productColor->update(['color_code' => $colorCode]);
        return $productColor;
    }

    public function deleteProductColor(ProductColor $productColor)
    {
        return $productColor->delete();
    }

    public function bulkDeleteProductColors(array $ids)
    {
        if (empty($ids)) {
            throw new \Exception('Heç bir rəng seçilməyib!');
        }
        $this->productColor->whereIn('id', $ids)->delete();
        return count($ids);
    }
}
