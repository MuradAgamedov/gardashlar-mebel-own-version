<?php

namespace App\Interfaces\Services\Front;

use App\Interfaces\Services\BaseServiceInterface;
use Illuminate\Database\Eloquent\Collection;
use App\Models\PhoneType;

interface PhoneTypeServiceInterface extends BaseServiceInterface
{
    public function getPhonesGroupedByTypes(int $languageId): Collection;
    public function getSpecialPhone(int $typeId): ?PhoneType;
}
