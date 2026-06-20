<?php

namespace App\Services\Admin;

use App\Interfaces\Repositories\Admin\CurrencyRepositoryInterface;
use App\Interfaces\Services\Admin\CurrencyServiceInterface;
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
