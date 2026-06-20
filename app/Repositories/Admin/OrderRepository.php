<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\OrderRepositoryInterface;
use App\Models\Order;
use App\Repositories\BaseRepository;
use App\Services\ImageService;

class OrderRepository extends BaseRepository implements OrderRepositoryInterface
{
    protected ImageService $imageService;

    public function __construct(Order $model, ImageService $imageService)
    {
        parent::__construct($model);
        $this->imageService = $imageService;
    }
}
