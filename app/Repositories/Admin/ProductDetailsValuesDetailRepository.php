<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\ProductDetailsValuesDetailRepositoryInterface;
use App\Models\ProductDetailsValuesDetail;
use App\Repositories\BaseRepository;
use App\Services\ImageService;

class ProductDetailsValuesDetailRepository extends BaseRepository implements ProductDetailsValuesDetailRepositoryInterface
{
    protected ImageService $imageService;

    public function __construct(ProductDetailsValuesDetail $model, ImageService $imageService)
    {
        parent::__construct($model);
        $this->imageService = $imageService;
    }

    public function getDetailsByValueId($detailValueId)
    {
        return ProductDetailsValuesDetail::with(['translates.language'])
            ->where('product_detail_value_id', $detailValueId)
            ->ordered()
            ->get();
    }
}
