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
}
