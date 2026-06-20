<?php

namespace App\Interfaces\Repositories\Front;

use App\Interfaces\Repositories\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

interface PhoneRepositoryInterface extends BaseRepositoryInterface
{
    public function getPhonesByType(int $typeId): Collection;
}
