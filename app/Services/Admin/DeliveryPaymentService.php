<?php

namespace App\Services\Admin;

use App\Interfaces\Repositories\Admin\DeliveryPaymentRepositoryInterface;
use App\Interfaces\Services\Admin\DeliveryPaymentServiceInterface;
use App\Services\BaseService;

class DeliveryPaymentService extends BaseService implements DeliveryPaymentServiceInterface
{
    protected DeliveryPaymentRepositoryInterface $deliveryPaymentRepository;

    public function __construct(DeliveryPaymentRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->deliveryPaymentRepository = $repository;
    }

    public function updateDeliveryPayment(array $translations, $languages)
    {
        return $this->deliveryPaymentRepository->updateDeliveryPayment($translations, $languages);
    }
}
