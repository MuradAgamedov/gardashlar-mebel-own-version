<?php

namespace App\Interfaces\Repositories\Admin;

use App\Interfaces\Repositories\BaseRepositoryInterface;

interface CurrencyRepositoryInterface extends BaseRepositoryInterface
{
    //

    public function getCurrencies();

    public function createCurrency(array $data);

    public function updateCurrency(\App\Models\Currency $currency, array $data);

    public function deleteCurrency(\App\Models\Currency $currency);

    public function reorderCurrencies(array $orders);

    public function toggleStatus(\App\Models\Currency $currency);

    public function bulkDelete(array $ids);

    public function bulkToggleStatus(array $ids);
}
