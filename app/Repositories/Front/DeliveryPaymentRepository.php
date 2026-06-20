<?php

namespace App\Repositories\Front;

use App\Interfaces\Repositories\Front\DeliveryPaymentRepositoryInterface;
use App\Models\DeliveryPayment;
use App\Repositories\BaseRepository;

class DeliveryPaymentRepository extends BaseRepository implements DeliveryPaymentRepositoryInterface
{
    public function __construct(DeliveryPayment $model)
    {
        parent::__construct($model);
    }
}
