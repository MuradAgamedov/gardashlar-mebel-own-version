<?php

namespace App\Interfaces\Repositories\Admin;

use App\Interfaces\Repositories\BaseRepositoryInterface;

interface DeliveryPaymentRepositoryInterface extends BaseRepositoryInterface
{
    public function updateDeliveryPayment(array $translations, $languages);
}
