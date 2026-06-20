<?php

namespace App\Services\Front;

use App\Interfaces\Repositories\Front\CreditCardRepositoryInterface;
use App\Interfaces\Services\Front\CreditCardServiceInterface;
use App\Services\BaseService;

class CreditCardService extends BaseService implements CreditCardServiceInterface
{
    protected CreditCardRepositoryInterface $creditCardRepository;

    public function __construct(CreditCardRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->creditCardRepository = $repository;
    }
}
