<?php

namespace App\Interfaces\Services\Admin;

use App\Interfaces\Services\BaseServiceInterface;

interface DeliveryPaymentServiceInterface extends BaseServiceInterface
{
    public function updateDeliveryPayment(array $translations, $languages);
}
