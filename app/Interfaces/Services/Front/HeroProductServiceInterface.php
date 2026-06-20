<?php

namespace App\Interfaces\Services\Front;

use App\Interfaces\Services\BaseServiceInterface;
use Illuminate\Database\Eloquent\Collection;

interface HeroProductServiceInterface extends BaseServiceInterface
{
    public function getActiveOrderedByCategory(int $categoryId, int $languageId): Collection;
}
