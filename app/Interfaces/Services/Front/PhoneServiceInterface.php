<?php

namespace App\Interfaces\Services\Front;

use App\Interfaces\Services\BaseServiceInterface;
use Illuminate\Database\Eloquent\Collection;

interface PhoneServiceInterface extends BaseServiceInterface
{
    public function getPhonesByType(int $typeId): Collection;
}
