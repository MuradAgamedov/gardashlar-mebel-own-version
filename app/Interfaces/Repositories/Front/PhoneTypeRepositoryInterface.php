<?php

namespace App\Interfaces\Repositories\Front;

use App\Interfaces\Repositories\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use App\Models\PhoneType;

interface PhoneTypeRepositoryInterface extends BaseRepositoryInterface
{
    public function getPhonesGroupedByTypes(int $languageId): Collection;
    public function getSpecialPhone(int $typeId): ?PhoneType;
}
