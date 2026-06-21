<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\CurrencyRepositoryInterface;
use App\Models\Currency;
use App\Repositories\BaseRepository;

class CurrencyRepository extends BaseRepository implements CurrencyRepositoryInterface
{
    public function __construct(Currency $model)
    {
        parent::__construct($model);
    }

    public function getCurrencies()
    {
        return $this->GetData($this->currency, [])->get();
    }

    public function createCurrency(array $data)
    {
        $maxOrder = $this->currency->max('order') ?? 0;
        
        return $this->currency->create([
            'code' => strtoupper($data['code']),
            'course' => $data['course'],
            'order' => $maxOrder + 1,
            'status' => $data['status'] ?? false
        ]);
    }

    public function updateCurrency(Currency $currency, array $data)
    {
        $currency->update([
            'code' => strtoupper($data['code']),
            'course' => $data['course'],
            'status' => $data['status'] ?? false
        ]);

        return $currency;
    }

    public function deleteCurrency(Currency $currency)
    {
        return $currency->delete();
    }

    public function reorderCurrencies(array $orders)
    {
        foreach ($orders as $order => $id) {
            $this->currency->where('id', $id)->update(['order' => $order]);
        }
        return true;
    }

    public function toggleStatus(Currency $currency)
    {
        $currency->update(['status' => !$currency->status]);
        return $currency;
    }

    public function bulkDelete(array $ids)
    {
        return $this->currency->whereIn('id', $ids)->delete();
    }

    public function bulkToggleStatus(array $ids)
    {
        $currencies = $this->currency->whereIn('id', $ids)->get();
        $updatedCount = 0;

        foreach ($currencies as $currency) {
            $currency->update(['status' => !$currency->status]);
            $updatedCount++;
        }

        return $updatedCount;
    }
}
