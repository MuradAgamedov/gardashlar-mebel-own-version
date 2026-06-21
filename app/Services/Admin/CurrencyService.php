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

    public function getCurrencies()
    {
        return $this->currencyRepository->getCurrencies();
    }

    public function createCurrency(array $data)
    {
        return $this->currencyRepository->createCurrency($data);
    }

    public function updateCurrency(\App\Models\Currency $currency, array $data)
    {
        return $this->currencyRepository->updateCurrency($currency, $data);
    }

    public function deleteCurrency(\App\Models\Currency $currency)
    {
        return $this->currencyRepository->deleteCurrency($currency);
    }

    public function reorderCurrencies(array $orders)
    {
        return $this->currencyRepository->reorderCurrencies($orders);
    }

    public function toggleStatus(\App\Models\Currency $currency)
    {
        return $this->currencyRepository->toggleStatus($currency);
    }

    public function bulkDelete(array $ids)
    {
        return $this->currencyRepository->bulkDelete($ids);
    }

    public function bulkToggleStatus(array $ids)
    {
        return $this->currencyRepository->bulkToggleStatus($ids);
    }
}
