<?php

namespace App\Services\Front;

use App\Interfaces\Repositories\Front\CurrencyRepositoryInterface;
use App\Interfaces\Services\Front\CurrencyServiceInterface;
use App\Services\BaseService;

class CurrencyService extends BaseService implements CurrencyServiceInterface
{
    protected CurrencyRepositoryInterface $currencyRepository;

    public function __construct(CurrencyRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->currencyRepository = $repository;
    }
}
