<?php

namespace App\Interfaces\Repositories\Front;

use App\Interfaces\Repositories\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

interface HeroProductRepositoryInterface extends BaseRepositoryInterface
{
    public function getActiveOrderedByCategory(int $categoryId, int $languageId): Collection;
}
