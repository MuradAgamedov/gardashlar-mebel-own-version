<?php

namespace App\Services\Admin;

use App\Interfaces\Repositories\Admin\CreditCardRepositoryInterface;
use App\Interfaces\Services\Admin\CreditCardServiceInterface;
use App\Services\BaseService;

class CreditCardService extends BaseService implements CreditCardServiceInterface
{
    protected CreditCardRepositoryInterface $CreditCardRepository;

    public function __construct(CreditCardRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->CreditCardRepository = $repository;
    }

    public function getCreditCards()
    {
        return $this->CreditCardRepository->getCreditCards();
    }

    public function createCreditCard(array $data, $imageFile = null)
    {
        return $this->CreditCardRepository->createCreditCard($data, $imageFile);
    }

    public function updateCreditCard(\App\Models\CreditCard $creditCard, array $data, $imageFile = null)
    {
        return $this->CreditCardRepository->updateCreditCard($creditCard, $data, $imageFile);
    }

    public function deleteCreditCard(\App\Models\CreditCard $creditCard)
    {
        return $this->CreditCardRepository->deleteCreditCard($creditCard);
    }

    public function reorderCreditCards(array $creditCards)
    {
        return $this->CreditCardRepository->reorderCreditCards($creditCards);
    }
}
