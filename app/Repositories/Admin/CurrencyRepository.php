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
}
