<?php
namespace App\Repositories;

use App\Models\Currency;
use App\Repositories\Global\BaseRepository;

class CurrencyRepository extends BaseRepository
{

    public function __construct(public Currency $currency)
    {
    }

    // məsələn bütün məlumatları gətirmək üçün
    public function getCurrencies()
    {
        return $this->GetData($this->currency, [])->get();
    }
}
