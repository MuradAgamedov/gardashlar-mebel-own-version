<?php

namespace App\Interfaces\Services\Admin;

use App\Interfaces\Services\BaseServiceInterface;

interface CreditCardServiceInterface extends BaseServiceInterface
{
    //

    public function getCreditCards();

    public function createCreditCard(array $data, $imageFile = null);

    public function updateCreditCard(\App\Models\CreditCard $creditCard, array $data, $imageFile = null);

    public function deleteCreditCard(\App\Models\CreditCard $creditCard);

    public function reorderCreditCards(array $creditCards);
}
