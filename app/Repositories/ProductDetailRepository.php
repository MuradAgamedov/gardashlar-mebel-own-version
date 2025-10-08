<?php
namespace App\Repositories;

use App\Models\ProductDetail;
use App\Repositories\Global\BaseRepository;

class ProductDetailRepository extends BaseRepository
{

    public function __construct(public ProductDetail $productDetail)
    {
    }

    // Məhsul üçün detailləri gətir
    public function getProductDetails($product)
    {
        return $product->details()->with(['translates.language'])->ordered()->get();
    }

    // Növbəti order nömrəsini gətir
    public function getNextOrder($productId)
    {
        return $this->productDetail->where('product_id', $productId)->max('order') + 1;
    }

    // Detail yarat
    public function createDetail($productId, $order)
    {
        return $this->productDetail->create([
            'product_id' => $productId,
            'order' => $order
        ]);
    }

    // Order-i yenilə
    public function updateOrder($detailId, $order)
    {
        return $this->productDetail->where('id', $detailId)->update(['order' => $order]);
    }

    // Çoxlu detailləri gətir
    public function getDetailsByIds($ids)
    {
        return $this->productDetail->whereIn('id', $ids)->get();
    }
}
