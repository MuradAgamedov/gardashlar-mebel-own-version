<?php

namespace App\Interfaces\Repositories\Admin;

use App\Interfaces\Repositories\BaseRepositoryInterface;

interface CreditCardRepositoryInterface extends BaseRepositoryInterface
{
    //

    public function getCreditCards();

    public function createCreditCard(array $data, $imageFile = null);

    public function updateCreditCard(\App\Models\CreditCard $creditCard, array $data, $imageFile = null);

    public function deleteCreditCard(\App\Models\CreditCard $creditCard);

    public function reorderCreditCards(array $creditCards);
}
