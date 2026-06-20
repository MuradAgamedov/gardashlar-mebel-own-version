<?php

namespace App\Services\Front;

use App\Interfaces\Repositories\Front\DeliveryPaymentRepositoryInterface;
use App\Interfaces\Services\Front\DeliveryPaymentServiceInterface;
use App\Services\BaseService;

class DeliveryPaymentService extends BaseService implements DeliveryPaymentServiceInterface
{
    protected DeliveryPaymentRepositoryInterface $deliveryPaymentRepository;

    public function __construct(DeliveryPaymentRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->deliveryPaymentRepository = $repository;
    }
}
