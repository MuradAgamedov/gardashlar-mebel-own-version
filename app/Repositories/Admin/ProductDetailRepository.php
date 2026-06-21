<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\ProductDetailRepositoryInterface;
use App\Models\ProductDetail;
use App\Repositories\BaseRepository;
use App\Services\ImageService;

class ProductDetailRepository extends BaseRepository implements ProductDetailRepositoryInterface
{
    protected ImageService $imageService;

    public function __construct(ProductDetail $model, ImageService $imageService)
    {
        parent::__construct($model);
        $this->imageService = $imageService;
    }

    public function getProductDetails($product)
    {
        return $product->details()->with(['translates.language'])->ordered()->get();
    }

    public function getNextOrder($productId)
    {
        return $this->productDetail->where('product_id', $productId)->max('order') + 1;
    }

    public function createDetail($productId, $order)
    {
        return $this->productDetail->create([
            'product_id' => $productId,
            'order' => $order
        ]);
    }

    public function updateOrder($detailId, $order)
    {
        return $this->productDetail->where('id', $detailId)->update(['order' => $order]);
    }

    public function getDetailsByIds($ids)
    {
        return $this->productDetail->whereIn('id', $ids)->get();
    }
}
