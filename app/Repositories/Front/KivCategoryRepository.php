<?php

namespace App\Repositories\Front;

use App\Interfaces\Repositories\Front\KivCategoryRepositoryInterface;
use App\Models\KivCategory;
use App\Repositories\BaseRepository;

class KivCategoryRepository extends BaseRepository implements KivCategoryRepositoryInterface
{
    public function __construct(KivCategory $model)
    {
        parent::__construct($model);
    }
}
