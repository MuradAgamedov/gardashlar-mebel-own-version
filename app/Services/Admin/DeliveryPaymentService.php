<?php

namespace App\Services\Admin;

use App\Interfaces\Repositories\Admin\DeliveryPaymentRepositoryInterface;
use App\Interfaces\Services\Admin\DeliveryPaymentServiceInterface;
use App\Services\BaseService;

class DeliveryPaymentService extends BaseService implements DeliveryPaymentServiceInterface
{
    protected DeliveryPaymentRepositoryInterface $DeliveryPaymentRepository;

    public function __construct(DeliveryPaymentRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->DeliveryPaymentRepository = $repository;
    }
}
