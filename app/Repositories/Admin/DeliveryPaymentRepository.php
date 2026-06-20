<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\DeliveryPaymentRepositoryInterface;
use App\Models\DeliveryPayment;
use App\Repositories\BaseRepository;
use App\Services\ImageService;

class DeliveryPaymentRepository extends BaseRepository implements DeliveryPaymentRepositoryInterface
{
    protected ImageService $imageService;

    public function __construct(DeliveryPayment $model, ImageService $imageService)
    {
        parent::__construct($model);
        $this->imageService = $imageService;
    }
}
